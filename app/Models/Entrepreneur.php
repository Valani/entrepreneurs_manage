<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entrepreneur extends Model
{
    protected $primaryKey = 'id_entrepreneurs';

    protected $fillable = [
        'name',
        'ipn',
        'iban',
        'tax_office_name',
        'group'
    ];

    public function kveds(): BelongsToMany
    {
        return $this->belongsToMany(Kved::class, 'kved_entrepreneurs', 'id_entrepreneurs', 'id_kved');
    }

    public function keys()
    {
        return $this->hasMany(Key::class, 'id_entrepreneurs', 'id_entrepreneurs');
    }

    public function reportEntrepreneurs(): HasMany
    {
        return $this->hasMany(ReportEntrepreneur::class, 'id_entrepreneurs', 'id_entrepreneurs');
    }

    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(Report::class, 'report_entrepreneurs', 'id_entrepreneurs', 'id_report')
            ->withPivot(['quarter', 'year', 'done'])
            ->withTimestamps();
    }

    public function financialData(): HasMany
    {
        return $this->hasMany(FinancialData::class, 'id_entrepreneurs', 'id_entrepreneurs');
    }

    public function taxPayments(): HasMany
    {
        return $this->hasMany(TaxPayment::class, 'id_entrepreneurs', 'id_entrepreneurs');
    }
}
