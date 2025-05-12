<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $fillable = [
        'id_entrepreneurs',
        'type',
        'date_start',
        'date_end'
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    public function entrepreneur()
    {
        return $this->belongsTo(Entrepreneur::class, 'id_entrepreneurs', 'id_entrepreneurs');
    }
}