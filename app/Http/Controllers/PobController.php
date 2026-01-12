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
        return view('pob.index');
    }

    public function create()
    {
        $businessUnits = Organization::select('business_unit')->distinct()->get();
        return view('pob.create', [
            'business_unit' => $businessUnits,
            'dateToday' => \Carbon\Carbon::now()->format('Y-m-d'),
        ]);
    }

    public function edit(PobRecords $pob)
    {
        // No need to json_decode if it's already cast to array
        return view('pob.edit', [
            'pob' => $pob,
        ]);
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
        return redirect()->route('pob.list')->with('success', 'POB record updated successfully!');
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
        // Validate required fields
        $request->validate([
            'business_unit' => 'required|string|max:255',
            'date' => 'required|date',
            'company' => 'required|array',
            'attendance' => 'required|array',
        ]);

        $companies = $request->input('company');
        $attendances = $request->input('attendance');

        // Combine company names with their attendance values
        $attendanceData = [];
        foreach ($companies as $index => $companyName) {
            $value = isset($attendances[$index]) ? (int) $attendances[$index] : 0;
            $attendanceData[$companyName] = $value;
        }

        // Create or update the POB record
        $record = PobRecords::updateOrCreate(
            [
                'business_unit' => $request->business_unit,
                'date' => $request->date,
            ],
            [
                'attendance_data' => $attendanceData,
            ],
        );

        return redirect()->route('pob.index')->with('success', 'Attendance data saved successfully!');
    }

    public function getChartData(Request $request)
    {
        $businessUnit = $request->input('business_unit', null);
        $year = (int) $request->input('year', now()->year);
        $week = (int) $request->input('week', now()->format('W'));

        // Convert year + week to start and end dates
        $dateFrom = \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek(); // Monday
        $dateTo = \Carbon\Carbon::now()->setISODate($year, $week)->endOfWeek(); // Sunday

        $pob = $this->getAveragePob($businessUnit, $dateFrom, $dateTo);
        $hbo = $this->getTotalHbo($businessUnit, $dateFrom, $dateTo);

        return response()->json([
            'POB' => $pob,
            'HBO' => $hbo,
            'business_unit' => $businessUnit,
            'year' => $year,
            'week' => $week,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ]);
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
                $totals[$company] = ($totals[$company] ?? 0) + $value;
                $counts[$company] = ($counts[$company] ?? 0) + 1;
            }
        }

        $averages = [];
        foreach ($totals as $company => $totalValue) {
            $averages[$company] = $totalValue / $counts[$company];
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

    public function getChartData2(Request $request)
    {
        $businessUnit = $request->input('business_unit', null);
        $year = (int) $request->input('year', now()->year);
        $week = (int) $request->input('week', now()->format('W'));

        // Start and end of the week
        $dateFrom = Carbon::now()->setISODate($year, $week)->startOfWeek(); // Monday
        $dateTo = Carbon::now()->setISODate($year, $week)->endOfWeek(); // Sunday

        $pob = $this->getPobAvgPerDay($businessUnit, $dateFrom, $dateTo);
        $hbo = $this->getHboSumPerDay($businessUnit, $dateFrom, $dateTo);

        return response()->json([
            'business_unit' => $businessUnit ?: 'All Business Units',
            'week_range' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
            'POB' => $pob,
            'HBO' => $hbo,
            'year' => $year,
            'week' => $week,
        ]);
    }

    /**
     * Break POB per day and include weekly average at the end
     */
    public function getPobAvgPerDay($businessUnit, $dateFrom, $dateTo)
    {
        $query = PobRecords::query()->whereDate('date', '>=', $dateFrom)->whereDate('date', '<=', $dateTo);

        if ($businessUnit) {
            $query->where('business_unit', $businessUnit);
        }

        $records = $query->get();

        $dailyTotals = [];
        $dailyCounts = [];

        foreach ($records as $record) {
            $dateKey = Carbon::parse($record->date)->format('Y-m-d');
            $data = is_array($record->attendance_data) ? $record->attendance_data : json_decode($record->attendance_data, true);
            $total = collect($data)->sum();

            $dailyTotals[$dateKey] = ($dailyTotals[$dateKey] ?? 0) + $total;
            $dailyCounts[$dateKey] = ($dailyCounts[$dateKey] ?? 0) + 1;
        }

        $dailyAvg = [];
        $sumTotal = 0;
        $dayCount = 0;

        // Fill days even if no records
        for ($date = $dateFrom->copy(); $date->lte($dateTo); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $avg = isset($dailyTotals[$key]) ? $dailyTotals[$key] / $dailyCounts[$key] : 0;
            $dailyAvg[] = [
                'date' => $key,
                'average' => round($avg, 2),
            ];
            $sumTotal += $avg;
            $dayCount++;
        }

        // Add weekly average
        $dailyAvg[] = [
            'date' => 'Ave POB / Total HBO',
            'average' => $dayCount > 0 ? round($sumTotal / $dayCount, 2) : 0,
        ];

        return $dailyAvg;
    }

    /**
     * Break HBO per day
     */
    public function getHboSumPerDay($businessUnit, $dateFrom, $dateTo)
    {
        $query = HboList::query()->whereDate('date_raised', '>=', $dateFrom)->whereDate('date_raised', '<=', $dateTo);

        if ($businessUnit) {
            $query->where('business_unit', $businessUnit);
        }

        $records = $query->get();

        $dailyTotals = [];

        foreach ($records as $record) {
            $dateKey = Carbon::parse($record->date_raised)->format('Y-m-d');
            $dailyTotals[$dateKey] = ($dailyTotals[$dateKey] ?? 0) + 1;
        }

        $dailySum = [];

        for ($date = $dateFrom->copy(); $date->lte($dateTo); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $dailySum[] = [
                'date' => $key,
                'total' => $dailyTotals[$key] ?? 0,
            ];
        }

        // Add weekly total
        $weeklyTotal = collect($dailySum)->sum('total');
        $dailySum[] = [
            'date' => 'Weekly Total',
            'total' => $weeklyTotal,
        ];

        return $dailySum;
    }

    ///////////////////////////

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

    public function list(Request $request)
    {
        $query = PobRecords::query();

        // Get all business units for default selection
        $businessUnits = PobRecords::select('business_unit')->distinct()->pluck('business_unit');

        // Determine which business_unit to filter by
        $selectedBU = $request->business_unit;

        // Apply Business Unit filter
        if ($selectedBU) {
            $query->where('business_unit', $selectedBU);
        }

        // Date filter
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $from = $request->date_from ?: '1900-01-01';
            $to = $request->date_to ?: now()->toDateString();
            $query->whereDate('date', '>=', $from)->whereDate('date', '<=', $to);
        }

        $poblist = $query->orderBy('date', 'desc')->paginate(10);
        $poblist->appends($request->all());

        return view('pob.list', compact('poblist', 'selectedBU', 'businessUnits'));
    }

    public function downloadTemplate($business_unit)
    {
        $companies = Organization::where('business_unit', $business_unit)->pluck('company_name')->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $header = array_merge(['business_unit', 'date'], $companies);
        $sheet->fromArray($header, null, 'A1');

        // Optional: Add example row
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

            // -------------------------------
            // 1. Validate headers
            // -------------------------------
            $headerRow = array_shift($rows); // remove first row
            $headers = array_values($headerRow); // convert A,B,C... to array index 0,1,2...

            if (strtolower(trim($headers[0])) !== 'business_unit' || strtolower(trim($headers[1])) !== 'date') {
                return back()->with('error', 'Invalid header format. First two columns must be: business_unit, date.');
            }

            // Attendance column headers (starting from column C)
            $attendanceHeaders = array_slice($headers, 2);

            // -------------------------------
            // 2. Process each row
            // -------------------------------
            foreach ($rows as $row) {
                // Convert to numeric index array
                $rowData = array_values($row);

                // Extract business_unit and date
                $business_unit = $rowData[0] ?? null;
                $date = $rowData[1] ?? null;

                if (!$business_unit || !$date) {
                    // Skip incomplete rows
                    continue;
                }

                // Convert Excel date properly (if numeric)
                if (is_numeric($date)) {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                }

                // -------------------------------
                // 3. Build attendance_data JSON
                // -------------------------------
                $attendance = [];
                foreach ($attendanceHeaders as $index => $companyName) {
                    $value = $rowData[$index + 2] ?? 0; // +2 because row[0]=BU, row[1]=date

                    // If empty → set 0
                    $attendance[$companyName] = is_numeric($value) ? $value : 0;
                }

                // -------------------------------
                // 4. Insert / Upsert
                // -------------------------------
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
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: Please check the template and try again');
        }
    }
}
