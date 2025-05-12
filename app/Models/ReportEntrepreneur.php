<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportEntrepreneur extends Model
{
    protected $fillable = [
        'id_entrepreneurs',
        'id_report',
        'quarter',
        'year',
        'done'
    ];

    protected $casts = [
        'done' => 'boolean'
    ];

    public function entrepreneur(): BelongsTo
    {
        return $this->belongsTo(Entrepreneur::class, 'id_entrepreneurs', 'id_entrepreneurs');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'id_report', 'id_report');
    }
}