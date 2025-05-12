<?php
namespace App\Imports;

use App\Models\FinancialData;
use App\Models\Entrepreneur;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class FinancialDataImport implements ToModel, WithHeadingRow
{
    protected $entrepreneur;

    public function __construct(Entrepreneur $entrepreneur)
    {
        $this->entrepreneur = $entrepreneur;
    }

    public function model(array $row)
    {
        // Map the CSV columns to our database structure
        return FinancialData::updateOrCreate(
            [
                'id_entrepreneurs' => $this->entrepreneur->id_entrepreneurs,
                'date' => Carbon::createFromFormat('m/d/Y', $row['orderdatetime'])->format('Y-m-d')
            ],
            [
                'cash' => $row['rlzsumcash'],
                'non_cash' => $row['rlzsumnoncash']
            ]
        );
    }
}