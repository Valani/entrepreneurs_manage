<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $primaryKey = 'id_report';
    
    protected $fillable = ['name'];

    public function reportEntrepreneurs(): HasMany
    {
        return $this->hasMany(ReportEntrepreneur::class, 'id_report', 'id_report');
    }
}