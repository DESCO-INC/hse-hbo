<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use App\Models\HboList;
use App\Models\Organization;
use App\Models\User;

class HboListController extends Controller
{
    public function index()
    {
        $organization = Organization::select('business_unit', 'company_name')->get();
        $business_unit = $organization->pluck('business_unit')->unique()->sort()->values();
        return view('hbo.index', compact('business_unit', 'organization'));
    }

    public function hboStatusCount(Request $request)
    {
        $query = HboList::query();

        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }
        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date_raised', [$request->date_from, $request->date_to]);
        }

        $counts = $query->count();

        $onGoing = (clone $query)->where('status', 'ONGOING')->count();
        $forVerification = (clone $query)->where('status', 'FOR VERIFICATION')->count();
        $close = (clone $query)->where('status', 'CLOSE')->count();

        return response()->json([
            'success' => true,
            'count' => $counts,
            'ongoing' => $onGoing,
            'forVerification' => $forVerification,
            'close' => $close,
        ]);
    }

    public function hboDataByDate(Request $request)
    {
        $query = HboList::query();
        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }

        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date_raised', [$request->date_from, $request->date_to]);
        }
        $data = $query->select(DB::raw('DATE(date_raised) as Date'), DB::raw('COUNT(*) as Count'))->groupBy('Date')->orderBy('Date')->get();

        return response()->json($data);
    }

    public function hboDataByCategory(Request $request)
    {
        $categoriesRaw = json_decode(file_get_contents(resource_path('json/categories.json')), true);

        $query = HboList::query();

        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }

        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date_raised', [$request->date_from, $request->date_to]);
        }

        $data = $query
            ->selectRaw('COALESCE(category, "Uncategorized") as category, COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($categoriesRaw) {
                // fallback if category doesn't exist in JSON
                $item->color = $categoriesRaw[$item->category]['color'] ?? '#000000';

                return [
                    'category' => $item->category,
                    'total' => (int) $item->total,
                    'color' => $item->color,
                ];
            })
            ->values();

        return response()->json($data);
    }

    public function hboDataByGroup(Request $request)
    {
        $query = HboList::query();
        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }

        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date_raised', [$request->date_from, $request->date_to]);
        }
        $data = $query
            ->selectRaw('COALESCE(company, "Uncategorized") as company, COUNT(*) as total')
            ->groupBy('company')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'company' => $item->company,
                    'total' => (int) $item->total,
                ];
            });

        return response()->json($data);
    }

    public function hboDataByType(Request $request)
    {
        $query = HboList::query();
        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }

        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date_raised', [$request->date_from, $request->date_to]);
        }
        $data = $query->selectRaw('type, COUNT(*) as total')->groupBy('type')->orderBy('total', 'desc')->get();

        return response()->json($data);
    }

    public function hboDataBySubCategory(Request $request)
    {
        $categoriesRaw = json_decode(file_get_contents(resource_path('json/categories.json')), true);
        $query = HboList::query();
        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }

        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date_raised', [$request->date_from, $request->date_to]);
        }

        $data = $query
            ->selectRaw('COALESCE(sub_category, "Uncategorized") as sub_category, category, COUNT(*) as total')
            ->groupBy('sub_category', 'category')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) use ($categoriesRaw) {
                $category = $item->category;
                $item->color = $categoriesRaw[$category]['subcategories'][$item->sub_category] ?? ($categoriesRaw[$category]['color'] ?? '#000000');
                return $item;
            });

        return response()->json($data);
    }

    public function hboDataByWeek(Request $request)
    {
        $query = HboList::query();
        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }

        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date_raised', [$request->date_from, $request->date_to]);
        }
        $data = $query
            ->selectRaw(
                "
        WEEK(date_raised, 1) as workweek,
        YEAR(date_raised) as year,
        COUNT(*) as total
    ",
            )
            ->groupBy('year', 'workweek')
            ->orderBy('year', 'asc')
            ->orderBy('workweek', 'asc')
            ->get()
            ->map(function ($item) {
                $week = (int) $item->workweek;

                return [
                    'week' => str_pad($week, 2, '0', STR_PAD_LEFT),
                    'total' => $item->total,
                ];
            });

        return response()->json($data);
    }

    public function hboDataByReporter(Request $request)
    {
        $baseQuery = HboList::query();

        if ($request->filled('business_unit')) {
            $baseQuery->where('business_unit', $request->business_unit);
        }

        if ($request->filled('company')) {
            $baseQuery->where('company', $request->company);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $baseQuery->whereBetween('date_raised', [$request->date_from, $request->date_to]);
        }

        // Top 5 reporters
        $topReporters = (clone $baseQuery)->select('reported_by', DB::raw('COUNT(*) as total'))->whereNotNull('reported_by')->groupBy('reported_by')->orderByDesc('total')->limit(5)->get();

        // Total number of distinct reporters
        $totalDistinct = (clone $baseQuery)->whereNotNull('reported_by')->distinct()->count('reported_by');

        return response()->json([
            'total_reporters' => $totalDistinct,
            'top_reporters' => $topReporters,
        ]);
    }

    public function hboWeeklyData(Request $request)
    {
        $baseQuery = HboList::query();
        // Filters
        if ($request->filled('business_unit')) {
            $baseQuery->where('business_unit', $request->business_unit);
        }
        if ($request->filled('company')) {
            $baseQuery->where('company', $request->company);
        }

        // Determine reference date (today or date_to)
        $dateRef = $request->filled('date_to') ? Carbon::parse($request->date_to) : Carbon::now();
        // Calculate week start/end dates for 3 weeks
        $weeks = [
            'Two Weeks Ago' => [
                'start' => $dateRef->copy()->startOfWeek()->subWeeks(2),
                'end' => $dateRef->copy()->startOfWeek()->subWeeks(2)->endOfWeek(),
            ],
            'Last Week' => [
                'start' => $dateRef->copy()->startOfWeek()->subWeek(),
                'end' => $dateRef->copy()->startOfWeek()->subWeek()->endOfWeek(),
            ],
            'This Week' => [
                'start' => $dateRef->copy()->startOfWeek(),
                'end' => $dateRef->copy()->endOfWeek(),
            ],
        ];

        $result = [];

        foreach ($weeks as $label => $range) {
            $weekStart = $range['start'];
            $weekEnd = $range['end'];
            // Clone base query for each week
            $weekQuery = (clone $baseQuery)
                ->whereBetween('date_raised', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                ->selectRaw('DATE(date_raised) as day, COUNT(*) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->pluck('total', 'day')
                ->toArray();
            // Fill in 0 for missing days
            $days = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $weekStart->copy()->addDays($i)->format('Y-m-d');
                $days[] = $weekQuery[$date] ?? 0;
            }
            $result[] = [
                'week_label' => $label,
                'range' => $weekStart->format('M d, Y') . ' - ' . $weekEnd->format('M d, Y'),
                'days' => $days,
                'total' => array_sum($days),
            ];
        }
        return response()->json($result);
    }

    public function list(Request $request)
    {
        $query = HboList::query();

        // SEARCH
        if ($request->form_type === 'search' && $request->filled('search')) {
            $query->where('id', $request->search);
        }

        // FILTER
        if ($request->form_type === 'filter') {
            if ($request->filled('business_unit')) {
                $query->where('business_unit', $request->business_unit);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('company')) {
                $query->where('company', $request->company);
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('date_raised', [$request->date_from, $request->date_to]);
            } elseif ($request->filled('date_from')) {
                $query->whereDate('date_raised', '>=', $request->date_from);
            } elseif ($request->filled('date_to')) {
                $query->whereDate('date_raised', '<=', $request->date_to);
            }
        }

        if (Auth::user()->credentials != 'SUPER_ADMIN') {
            $query->where('business_unit', Auth::user()->business_unit);
        }

        $organization = Organization::select('business_unit', 'company_name')->get();
        $status = HboList::select('status')->distinct()->pluck('status');
        $business_unit = $organization->pluck('business_unit')->unique()->sort()->values();
        $hboList = $query->orderBy('id', 'desc')->paginate(10)->appends($request->all());
        return view('hbo.list', compact('hboList', 'business_unit', 'organization', 'status'));
    }

    public function dataForInput()
    {
        $swa_sro = json_decode(file_get_contents(resource_path('json/swa_sro.json')), true);
        $organization = Organization::select('business_unit', 'company_name')->get();
        $categoriesRaw = json_decode(file_get_contents(resource_path('json/categories.json')), true);
        $business_unit = ['Business_unit' => $organization->pluck('business_unit')->unique()->toArray()];
        $categories = ['Categories' => array_keys($categoriesRaw)];
        $types = json_decode(file_get_contents(resource_path('json/types.json')), true);
        $users = ['Users' => User::all()->pluck('name')->toArray()];
        $merged = array_merge($types, $users, $categories, $business_unit, [
            'SWA' => $swa_sro['SWA'],
            'SRO' => $swa_sro['SRO'],
        ]);

        return [
            'organization' => $organization,
            'categoriesRaw' => $categoriesRaw,
            'data' => $merged,
        ];
    }

    public function create()
    {
        $data = $this->dataForInput();
        return view('hbo.create', $data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_unit' => 'required',
            'company' => 'required',
            'type' => 'required',
            'category' => 'required',
            'sub_category' => 'required',
            'date_raised' => 'required|date',
            'date_due' => 'required|date|after_or_equal:date_raised',
            'SWA' => 'required',
            'SRO' => 'required',
            'hbo_photo' => 'nullable', // if uploading multiple photos
            'reported_by' => 'required',
            'reported_to' => 'required',
            'hazard_description' => 'required',
            'recommendation' => 'required',
        ]);

        // Convert hbo_photo to JSON if not null
        if (!empty($data['hbo_photo'])) {
            $data['hbo_photo'] = json_encode($data['hbo_photo']);
        }

        \App\Models\HboList::create(
            array_merge($data, [
                'status' => 'ONGOING',
                'created_by' => Auth::user()->name,
            ]),
        );

        return redirect()->route('hbo.list')->with('success', 'Hazard record created successfully.');
    }

    public function edit(HboList $hbo)
    {
        $data = $this->dataForInput();
        $typesData = json_decode(file_get_contents(resource_path('json/types.json')), true);
        $types = $typesData['Types'] ?? [];
        $categoriesData = json_decode(file_get_contents(resource_path('json/categories.json')), true);
        $swa_sro = json_decode(file_get_contents(resource_path('json/swa_sro.json')), true);

        return view('hbo.edit', $data, [
            'hbo' => $hbo,
        ]);
    }

    public function update(Request $request, HboList $hbo)
    {
        // ✅ Validate editable fields including Action fields
        $validated = $request->validate([
            'business_unit' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'sub_category' => 'nullable|string|max:255',
            'date_raised' => 'nullable|date',
            'date_due' => 'nullable|date',
            'SWA' => 'nullable|string|max:255',
            'SRO' => 'nullable|string|max:255',
            'hbo_photo' => 'nullable',
            'reported_by' => 'nullable|string|max:255',
            'reported_to' => 'nullable|string|max:255',
            'hazard_description' => 'nullable|string|max:1000',
            'recommendation' => 'nullable|string|max:1000',
            'action_by' => 'nullable|string|max:255',
            'action_date' => 'nullable|date',
            'action_remarks' => 'nullable|string|max:1000',
            'verified_by' => 'nullable|string|max:255',
            'verified_date' => 'nullable|date',
            'verified_remarks' => 'nullable|string|max:1000',
        ]);

        // ✅ Convert comma-separated string to array for photos
        if (!empty($validated['hbo_photo'])) {
            $validated['hbo_photo'] = array_map('trim', explode(',', $validated['hbo_photo']));
        }

        // ✅ Force update using Eloquent update() method
        $hbo->update($validated);

        return redirect()->route('hbo.edit', $hbo->id)->with('success', 'HBO information and Action updated successfully.');
    }

    public function takeAction(Request $request, HboList $hbo)
    {
        // Validate the input
        $validated = $request->validate([
            'action_by' => 'required|string|max:255',
            'action_date' => 'required|date',
            'action_remarks' => 'required|string|max:1000',
        ]);

        // Update HBO action details
        $hbo->action_by = $validated['action_by'];
        $hbo->action_date = $validated['action_date'];
        $hbo->action_remarks = $validated['action_remarks'];

        // Move status to For Verification
        $hbo->status = 'FOR VERIFICATION';
        $hbo->save();

        // Redirect back with success message
        return redirect()->route('hbo.edit', $hbo->id)->with('success', 'Action details submitted successfully and moved to For Verification.');
    }

    public function Verification(Request $request, HboList $hbo)
    {
        // Validate the input
        $validated = $request->validate([
            'verified_by' => 'required|string|max:255',
            'verified_date' => 'required|date',
            'verified_remarks' => 'required|string|max:1000',
        ]);

        // Update HBO action details
        $hbo->verified_by = $validated['verified_by'];
        $hbo->verified_date = $validated['verified_date'];
        $hbo->verified_remarks = $validated['verified_remarks'];

        // Move status to For Verification
        $hbo->status = 'CLOSE';
        $hbo->save();

        // Redirect back with success message
        return redirect()->route('hbo.edit', $hbo->id)->with('success', 'Verification details submitted successfully.');
    }

    public function destroy($id)
    {
        $hbo = HboList::findOrFail($id);
        $hbo->delete();

        return redirect()->route('hbo.list')->with('success', 'HBO record deleted successfully.');
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathName());
            $sheet = $spreadsheet->getActiveSheet();

            $rowIterator = $sheet->getRowIterator();

            $expectedHeaders = [
                'A' => 'business_unit',
                'B' => 'company',
                'C' => 'type',
                'D' => 'category',
                'E' => 'sub_category',
                'F' => 'hazard_description',
                'G' => 'recommendation',
                'H' => 'reported_by',
                'I' => 'reported_to',
                'J' => 'date_raised',
                'K' => 'date_due',
                'L' => 'SWA',
                'M' => 'SRO',
                'N' => 'action_by',
                'O' => 'action_date',
                'P' => 'action_remarks',
                'Q' => 'verified_by',
                'R' => 'verified_date',
                'S' => 'verified_remarks',
                'T' => 'status',
            ];

            // Validate headers (only first row)
            $headerRow = $rowIterator->current()->getCellIterator();
            $headerRow->setIterateOnlyExistingCells(false);

            $colIndex = 'A';
            foreach ($headerRow as $cell) {
                $value = strtolower(trim($cell->getValue()));
                $expected = strtolower($expectedHeaders[$colIndex] ?? '');

                if ($expected && $value !== $expected) {
                    return back()->with('error', "Incorrect header in column {$colIndex}. Expected '{$expected}', found '{$value}'.");
                }
                $colIndex++;
            }

            $formatDate = function ($value) {
                if (empty($value)) {
                    return null;
                }
                if (is_numeric($value)) {
                    return Date::excelToDateTimeObject($value)->format('Y-m-d');
                }
                try {
                    return Carbon::parse($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };

            $clean = fn($v) => is_string($v) ? strtoupper(preg_replace('/\s+/', ' ', trim($v))) : $v;

            $batch = [];
            $batchSize = 300; // smaller batch = safer
            $auth = Auth::user()->name;

            $rowNumber = 0;
            foreach ($rowIterator as $row) {
                $rowNumber++;

                // Skip header
                if ($rowNumber == 1) {
                    continue;
                }

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $clean($cell->getValue());
                }

                // Ignore fully empty row
                if (count(array_filter($cells)) == 0) {
                    continue;
                }

                // ===== Add this block for TYPE normalization =====
                $allowedTypes = ['SAFE BEHAVIOUR', 'UNSAFE BEHAVIOUR', 'SAFE CONDITION', 'UNSAFE CONDITION'];

                $typeValue = strtoupper(trim($cells[2] ?? ''));

                // Exact match first
                if (!in_array($typeValue, $allowedTypes)) {
                    // Fuzzy match
                    $closest = null;
                    $shortest = 9999;
                    foreach ($allowedTypes as $type) {
                        $lev = levenshtein($typeValue, $type);
                        if ($lev < $shortest) {
                            $shortest = $lev;
                            $closest = $type;
                        }
                    }
                    if ($shortest <= 5) {
                        // adjust threshold
                        $typeValue = $closest;
                    } else {
                        return back()->with('error', "Invalid TYPE value '{$cells[2]}' on row {$rowNumber}. Allowed values: " . implode(', ', $allowedTypes));
                    }
                }
                // ===== End TYPE normalization =====

                $batch[] = [
                    'business_unit' => $cells[0] ?? null,
                    'company' => $cells[1] ?? null,
                    'type' => $typeValue, // <-- use normalized value
                    'category' => $cells[3] ?? null,
                    'sub_category' => $cells[4] ?? null,
                    'hazard_description' => $cells[5] ?? null,
                    'recommendation' => $cells[6] ?? null,
                    'reported_by' => $cells[7] ?? null,
                    'reported_to' => $cells[8] ?? null,
                    'date_raised' => $formatDate($cells[9] ?? null),
                    'date_due' => $formatDate($cells[10] ?? null),
                    'SWA' => $cells[11] ?? null,
                    'SRO' => $cells[12] ?? null,
                    'action_by' => $cells[13] ?? null,
                    'action_date' => $formatDate($cells[14] ?? null),
                    'action_remarks' => $cells[15] ?? null,
                    'verified_by' => $cells[16] ?? null,
                    'verified_date' => $formatDate($cells[17] ?? null),
                    'verified_remarks' => $cells[18] ?? null,
                    'status' => $cells[19] ?? null,
                    'created_by' => $auth,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($batch) >= $batchSize) {
                    HboList::insert($batch);
                    $batch = [];
                }
            }

            if ($batch) {
                HboList::insert($batch);
            }

            return back()->with('success', 'Excel data imported successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        // ✅ Allow script to run for large datasets
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $table = 'hbo_list';
        $columns = Schema::getColumnListing($table);

        // Define which columns are dates
        $dateColumns = ['date_raised', 'date_due', 'action_date', 'verified_date', 'created_at', 'updated_at'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write header row
        $sheet->fromArray($columns, null, 'A1');
        $row = 2;

        $query = HboList::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('business_unit')) {
            $query->where('business_unit', $request->business_unit);
        }
        if ($request->filled('company')) {
            $query->where('company', $request->company);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date_raised', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_raised', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where('id', $request->search);
        }

        // ✅ Batch export 500 rows at a time
        $query->orderBy('id')->chunk(2000, function ($hbos) use ($sheet, &$row, $columns, $dateColumns) {
            foreach ($hbos as $hbo) {
                $data = [];
                foreach ($columns as $col) {
                    $value = $hbo->{$col};
                    if ($value && in_array($col, $dateColumns)) {
                        $value = Date::PHPToExcel(strtotime($value));
                    }
                    $data[] = $value;
                }
                $sheet->fromArray($data, null, 'A' . $row);
                $row++;
            }
            unset($hbos); // free memory
        });

        // ✅ Set proper date format per column (apply to entire column)
        foreach ($dateColumns as $dateCol) {
            if (in_array($dateCol, $columns)) {
                $colIndex = array_search($dateCol, $columns) + 1;
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet
                    ->getStyle($colLetter . '2:' . $colLetter . $row)
                    ->getNumberFormat()
                    ->setFormatCode('yyyy-mm-dd');
            }
        }

        // ✅ Replace auto-size with fixed width (much faster)
        $maxColumns = min(count($columns), 16384); // PhpSpreadsheet max
        for ($i = 1; $i <= $maxColumns; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setWidth(20);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'hbo_list_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}
