<?php

namespace App\Http\Controllers;

use App\Models\Entrepreneur;
use App\Models\FinancialData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FinancialDataImport;
use App\Exports\FinancialDataExport;
use App\Services\FileConverterService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class FinancialDataController extends Controller
{
    protected $fileConverter;

    public function __construct(FileConverterService $fileConverter)
    {
        $this->fileConverter = $fileConverter;
    }

    public function index(Entrepreneur $entrepreneur, Request $request)
    {
        $viewMode = $request->get('view_mode', 'month');
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $quarter = $request->get('quarter', ceil($month / 3));
        $entrepreneurs = Entrepreneur::orderBy('name')->get();

        switch ($viewMode) {
            case 'month':
                $data = $this->getMonthlyData($entrepreneur, $year, $month);
                break;
            case 'quarter':
                $data = $this->getQuarterlyData($entrepreneur, $year, $quarter);
                break;
            case 'year':
                $data = $this->getYearlyData($entrepreneur, $year);
                break;
            default:
                $data = $this->getMonthlyData($entrepreneur, $year, $month);
        }

        return view('entrepreneurs.financial-data', compact(
            'entrepreneur',
            'entrepreneurs',
            'data',
            'viewMode',
            'year',
            'month',
            'quarter'
        ));
    }

    private function getMonthlyData($entrepreneur, $year, $month)
    {
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $data = [];

        // Get all financial data for the month
        $records = FinancialData::where('id_entrepreneurs', $entrepreneur->id_entrepreneurs)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function($item) {
                return $item->date->day;
            });

        // Create array with all days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $record = $records->get($day);

            $data[$day] = [
                'date' => $date,
                'cash' => $record ? $record->cash : 0,
                'non_cash' => $record ? $record->non_cash : 0,
            ];
        }

        return $data;
    }

    private function getQuarterlyData($entrepreneur, $year, $quarter)
    {
        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $quarter * 3;
        
        return FinancialData::getQuarterlyTotals($entrepreneur->id_entrepreneurs, $year, $quarter);
    }

    private function getYearlyData($entrepreneur, $year)
    {
        return FinancialData::getYearlyTotals($entrepreneur->id_entrepreneurs, $year);
    }

    public function update(Request $request, Entrepreneur $entrepreneur)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'cash' => 'required|numeric|min:0',
            'non_cash' => 'required|numeric|min:0'
        ]);

        // If both values are 0, delete the record
        if ($validated['cash'] == 0 && $validated['non_cash'] == 0) {
            FinancialData::where('id_entrepreneurs', $entrepreneur->id_entrepreneurs)
                ->where('date', $validated['date'])
                ->delete();

            return response()->json(['success' => true, 'message' => 'Record deleted']);
        }

        // Otherwise, update or create the record
        FinancialData::updateOrCreate(
            [
                'id_entrepreneurs' => $entrepreneur->id_entrepreneurs,
                'date' => $validated['date']
            ],
            [
                'cash' => $validated['cash'],
                'non_cash' => $validated['non_cash']
            ]
        );

        return response()->json(['success' => true, 'message' => 'Запис оновлено']);
    }

    public function import(Request $request, Entrepreneur $entrepreneur)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $tempFile = null;

            // If the file is Excel format, convert it to CSV
            if (in_array($file->getClientOriginalExtension(), ['xlsx', 'xls'])) {
                $tempFile = $this->fileConverter->convertToCSV($file);
                // Create a new UploadedFile instance from the temporary CSV
                $file = new \Illuminate\Http\UploadedFile(
                    $tempFile,
                    'converted.csv',
                    'text/csv',
                    null,
                    true
                );
            }

            // Import the data
            Excel::import(new FinancialDataImport($entrepreneur), $file);

            // Clean up temporary file if it exists
            if ($tempFile) {
                $this->fileConverter->cleanup($tempFile);
            }

            return response()->json(['success' => true, 'message' => 'Дані успішно імпортовано']);
        } catch (\Exception $e) {
            // Clean up temporary file if it exists and there was an error
            if (isset($tempFile) && $tempFile) {
                $this->fileConverter->cleanup($tempFile);
            }

            \Log::error('Import failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Помилка імпорту: ' . $e->getMessage()], 422);
        }
    }

    public function export(Entrepreneur $entrepreneur, Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        return Excel::download(
            new FinancialDataExport($entrepreneur, $year, $month),
            "financial-data-{$entrepreneur->name}-{$year}-{$month}.xlsx"
        );
    }
}