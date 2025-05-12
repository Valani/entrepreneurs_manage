<?php
namespace App\Exports;

use App\Models\FinancialData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinancialDataExport implements FromCollection, WithHeadings
{
    protected $entrepreneur;
    protected $year;
    protected $month;

    public function __construct($entrepreneur, $year, $month)
    {
        $this->entrepreneur = $entrepreneur;
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return FinancialData::where('id_entrepreneurs', $this->entrepreneur->id_entrepreneurs)
            ->whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'Date' => $item->date->format('Y-m-d'),
                    'Cash' => $item->cash,
                    'Non-Cash' => $item->non_cash,
                    'Total' => $item->cash + $item->non_cash
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Cash',
            'Non-Cash',
            'Total'
        ];
    }
}