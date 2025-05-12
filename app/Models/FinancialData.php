<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialData extends Model
{
    protected $fillable = [
        'id_entrepreneurs',
        'date',
        'cash',
        'non_cash'
    ];

    protected $casts = [
        'date' => 'date',
        'cash' => 'decimal:2',
        'non_cash' => 'decimal:2'
    ];

    public function entrepreneur(): BelongsTo
    {
        return $this->belongsTo(Entrepreneur::class, 'id_entrepreneurs', 'id_entrepreneurs');
    }

    // Helper method to get monthly totals
    public static function getMonthlyTotals($entrepreneurId, $year, $month)
    {
        return self::where('id_entrepreneurs', $entrepreneurId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw('SUM(cash) as total_cash, SUM(non_cash) as total_non_cash')
            ->first();
    }

    // Helper method to get quarterly totals
    public static function getQuarterlyTotals($entrepreneurId, $year, $quarter)
    {
        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $quarter * 3;

        return self::where('id_entrepreneurs', $entrepreneurId)
            ->whereYear('date', $year)
            ->whereMonth('date', '>=', $startMonth)
            ->whereMonth('date', '<=', $endMonth)
            ->selectRaw('MONTH(date) as month, SUM(cash) as total_cash, SUM(non_cash) as total_non_cash')
            ->groupBy('month')
            ->get();
    }

    // Helper method to get yearly totals
    public static function getYearlyTotals($entrepreneurId, $year)
    {
        return self::where('id_entrepreneurs', $entrepreneurId)
            ->whereYear('date', $year)
            ->selectRaw('MONTH(date) as month, SUM(cash) as total_cash, SUM(non_cash) as total_non_cash')
            ->groupBy('month')
            ->get();
    }
}