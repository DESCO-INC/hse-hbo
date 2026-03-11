<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use App\Models\HboList;
use App\Models\Organization;
use App\Models\PobCompany;
use App\Models\PobRecords;

class PobController extends Controller
{
    public function index()
    {
        $business_unit = Organization::pluck('business_unit')->unique()->sort()->values();
        $records = PobRecords::select('date')->get();
        $years = [];
        $weeks = [];
        foreach ($records as $record) {
            if ($record->date) {
                $date = \Carbon\Carbon::parse($record->date);
                $years[] = $date->year;
                $weeks[] = $date->format('W'); // ISO-8601 week number (01-53)
            }
        }
        $years = collect($years)->unique()->sortDesc()->values();
        $weeks = collect($weeks)->unique()->sort()->values();
        return view('pob.index', compact('business_unit', 'years', 'weeks'));
    }

    public function PobHboAveData(Request $request)
    {
        $businessUnit = $request->input('business_unit', null);
        $year = (int) $request->input('year', now()->year);
        $week = (int) $request->input('week', now()->format('W'));

        // Convert year + week to start and end dates
        $dateFrom = \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek();
        $dateTo = \Carbon\Carbon::now()->setISODate($year, $week)->endOfWeek();

        $pob = $this->getAveragePob($businessUnit, $dateFrom, $dateTo);
        $hbo = $this->getTotalHbo($businessUnit, $dateFrom, $dateTo);

        return response()->json([
            'POB' => $pob,
            'HBO' => $hbo,
        ]);
    }

    public function PobHboWeeklyData(Request $request)
    {
        $businessUnit = $request->input('business_unit', null);
        $year = (int) $request->input('year', now()->year);
        $week = (int) $request->input('week', now()->format('W'));

        $dateFrom = Carbon::now()->setISODate($year, $week)->startOfWeek(); // Monday
        $dateTo = Carbon::now()->setISODate($year, $week)->endOfWeek(); // Sunday

        // --- POB: Average per day ---
        $pobRecords = PobRecords::query()
            ->when($businessUnit, fn($q) => $q->where('business_unit', $businessUnit))
            ->whereBetween('date', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')])
            ->get();

        $dailyPobTotals = [];
        $dailyPobCounts = [];

        foreach ($pobRecords as $record) {
            $dateKey = Carbon::parse($record->date)->format('Y-m-d');
            $data = is_array($record->attendance_data) ? $record->attendance_data : json_decode($record->attendance_data, true);
            $total = collect($data)->sum();

            $dailyPobTotals[$dateKey] = ($dailyPobTotals[$dateKey] ?? 0) + $total;
            $dailyPobCounts[$dateKey] = ($dailyPobCounts[$dateKey] ?? 0) + 1;
        }

        $pobData = [];
        $sumPob = 0;
        $daysCount = 0;

        for ($date = $dateFrom->copy(); $date->lte($dateTo); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $avg = ($dailyPobTotals[$key] ?? 0) / ($dailyPobCounts[$key] ?? 1);
            $pobData[] = [
                'date' => $key,
                'average' => round($avg, 2),
            ];
            $sumPob += $avg;
            $daysCount++;
        }

        $pobTotalAve = $daysCount ? round($sumPob / $daysCount, 2) : 0;

        // --- HBO: Total per day ---
        $hboRecords = HboList::query()
            ->when($businessUnit, fn($q) => $q->where('business_unit', $businessUnit))
            ->whereBetween('date_raised', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')])
            ->get();

        $dailyHboTotals = [];

        foreach ($hboRecords as $record) {
            $key = Carbon::parse($record->date_raised)->format('Y-m-d');
            $dailyHboTotals[$key] = ($dailyHboTotals[$key] ?? 0) + 1;
        }

        $hboData = [];
        $weeklyHboTotal = 0;

        for ($date = $dateFrom->copy(); $date->lte($dateTo); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $total = $dailyHboTotals[$key] ?? 0;
            $hboData[] = [
                'date' => $key,
                'total' => $total,
            ];
            $weeklyHboTotal += $total;
        }

        $hboTotalCount = $weeklyHboTotal;

        return response()->json([
            'POB' => $pobData,
            'HBO' => $hboData,
            'POB_average' => $pobTotalAve,
            'HBO_total' => $hboTotalCount,
        ]);
    }

    public function list(Request $request)
    {
        $business_unit = Organization::get()->pluck('business_unit')->unique()->sort()->values();

        $query = PobRecords::query();
        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $from = $request->date_from ?: '1900-01-01';
            $to = $request->date_to ?: now()->toDateString();
            $query->whereDate('date', '>=', $from)->whereDate('date', '<=', $to);
        }

        $poblist = $query->orderBy('date', 'desc')->paginate(10);
        return view('pob.list', compact('poblist', 'business_unit'));
    }

    public function create()
    {
        $organization = Organization::select('business_unit', 'company_name')->get();
        $business_unit = $organization->pluck('business_unit')->unique()->sort()->values();
        return view('pob.create', compact('business_unit', 'organization'));
    }

    public function edit(PobRecords $pob)
    {
        $organization = Organization::select('business_unit', 'company_name')->get();
        $business_unit = $organization->pluck('business_unit')->unique()->sort()->values();
        return view('pob.edit', compact('business_unit', 'organization', 'pob'));
    }

    public function update(Request $request, PobRecords $pob)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'business_unit' => 'required|string',
            'date' => 'required|date',
            'company' => 'required|array',
            'attendance' => 'required|array',
        ]);

        // Build the attendance_data array
        $attendanceData = [];
        foreach ($validated['company'] as $index => $company) {
            $attendanceData[$company] = (int) ($validated['attendance'][$index] ?? 0);
        }

        // Update the record
        $pob->update([
            'business_unit' => $validated['business_unit'],
            'date' => $validated['date'],
            'attendance_data' => $attendanceData, // will be cast to JSON automatically
        ]);

        // Redirect back to the index page or wherever you want
        return back()->with('success', 'POB record updated successfully!');
    }

    public function destroy(PobRecords $pob)
    {
        try {
            $pob->delete();

            return redirect()->route('pob.list')->with('success', 'POB record deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('pob.list')->with('error', 'Failed to delete POB record.');
        }
    }

    public function getPobRecords(Request $request)
    {
        $businessUnit = $request->input('business_unit', null);
        $dateFrom = $request->input('date_from', now()->startOfYear()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $perPage = 5; // Force 5 per page

        $query = PobRecords::query();

        // Filter by date (ignore time part)
        $query->whereBetween(\DB::raw('DATE(date)'), [$dateFrom, $dateTo]);

        // Only filter by business unit if provided
        if (!empty($businessUnit)) {
            $query->where('business_unit', $businessUnit);
        }

        // Paginate
        $records = $query->orderBy('date', 'desc')->paginate($perPage);

        // Transform records for frontend
        $data = $records->map(function ($record) {
            $attendance = $record->attendance_data ?? [];
            $total = array_sum($attendance);

            return [
                'id' => $record->id,
                'date' => $record->date->format('Y-m-d'),
                'business_unit' => $record->business_unit,
                'total' => $total,
                'attendance_data' => $attendance,
            ];
        });

        // Return JSON response with pagination
        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'business_unit' => 'required|string|max:255',
                'date' => 'required|date',
                'company' => 'required|array',
                'attendance' => 'required|array',
            ]);

            $attendanceData = [];
            foreach ($request->company as $index => $companyName) {
                $attendanceData[$companyName] = (int) ($request->attendance[$index] ?? 0);
            }

            PobRecords::create([
                'business_unit' => $request->business_unit,
                'date' => $request->date,
                'attendance_data' => $attendanceData,
            ]);

            return redirect()->route('pob.list')->with('success', 'Attendance data saved successfully!');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->with('error', 'Data with this Business Unit and Date already exist in database.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function getAveragePob($businessUnit, $dateFrom, $dateTo)
    {
        $query = PobRecords::query()->whereDate('date', '>=', $dateFrom)->whereDate('date', '<=', $dateTo);

        if (!empty($businessUnit)) {
            $query->where('business_unit', $businessUnit);
        }

        $records = $query->get();

        $totals = [];
        $counts = [];

        foreach ($records as $record) {
            $data = is_array($record->attendance_data) ? $record->attendance_data : json_decode($record->attendance_data, true);

            foreach ($data as $company => $value) {
                $totals[$company] = ($totals[$company] ?? 0) + (int) $value;
                $counts[$company] = ($counts[$company] ?? 0) + 1;
            }
        }

        $averages = [];
        foreach ($totals as $company => $totalValue) {
            $averages[$company] = ceil($totalValue / $counts[$company]); // round up, remove decimals
        }

        return $averages;
    }

    public function getTotalHbo($businessUnit, $dateFrom, $dateTo)
    {
        $query = HboList::query()->whereDate('date_raised', '>=', $dateFrom)->whereDate('date_raised', '<=', $dateTo);

        if (!empty($businessUnit)) {
            $query->where('business_unit', $businessUnit);
        }

        $records = $query->get();

        $totals = [];

        foreach ($records as $record) {
            $company = strtoupper($record->company); // normalize key
            $totals[$company] = ($totals[$company] ?? 0) + 1; // increment total occurrences
        }

        return $totals; // this now returns the sum of HBO per company
    }

    public function business_unit()
    {
        $business_units = PobRecords::select('business_unit')->distinct()->pluck('business_unit');

        return response()->json($business_units);
    }

    public function availableYearsAndWeeks()
    {
        $records = PobRecords::select('date')->get();

        $years = [];
        $weeks = [];

        foreach ($records as $record) {
            if ($record->date) {
                $date = \Carbon\Carbon::parse($record->date);
                $years[] = $date->year;
                $weeks[] = $date->format('W'); // ISO-8601 week number (01-53)
            }
        }

        $years = collect($years)->unique()->sortDesc()->values(); // unique years, latest first
        $weeks = collect($weeks)->unique()->sort()->values(); // unique weeks sorted ascending

        return response()->json([
            'years' => $years,
            'weeks' => $weeks,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        $business_unit = $request->query('business_unit'); // now from ?business_unit=XXX

        $companies = Organization::where('business_unit', $business_unit)->pluck('company_name')->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $header = array_merge(['business_unit', 'date'], $companies);
        $sheet->fromArray($header, null, 'A1');

        // Example row
        $exampleRow = array_merge([$business_unit, now()->format('Y-m-d')], array_fill(0, count($companies), 0));
        $sheet->fromArray($exampleRow, null, 'A2');

        // Prepare download
        $filename = 'POB_Template_' . $business_unit . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
        exit();
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows)) {
                return back()->with('error', 'Excel file is empty.');
            }

            // 1. Validate headers
            $headerRow = array_shift($rows); // remove first row
            $headers = array_values($headerRow); // convert A,B,C... to array index 0,1,2...

            if (strtolower(trim($headers[0])) !== 'business_unit' || strtolower(trim($headers[1])) !== 'date') {
                return back()->with('error', 'Invalid header format. First two columns must be: business_unit, date.');
            }

            $attendanceHeaders = array_slice($headers, 2);

            // 2. Process each row
            foreach ($rows as $row) {
                $rowData = array_values($row);

                $business_unit = $rowData[0] ?? null;
                $date = $rowData[1] ?? null;

                if (!$business_unit || !$date) {
                    continue;
                }

                if (is_numeric($date)) {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                }

                $attendance = [];
                foreach ($attendanceHeaders as $index => $companyName) {
                    $value = $rowData[$index + 2] ?? 0;
                    $attendance[$companyName] = is_numeric($value) ? $value : 0;
                }

                // 3. Insert / Upsert
                \App\Models\PobRecords::updateOrCreate(
                    [
                        'business_unit' => $business_unit,
                        'date' => $date,
                    ],
                    [
                        'attendance_data' => $attendance,
                    ],
                );
            }

            return back()->with('success', 'Excel uploaded successfully!');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->with('error', 'Some data is already exist in database. Please review your file.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong while uploading the Excel file.');
        }
    }
}
