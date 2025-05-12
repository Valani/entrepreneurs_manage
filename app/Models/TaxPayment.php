<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxPayment extends Model
{
    protected $fillable = [
        'id_entrepreneurs',
        'date',
        'amount',
        'description'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    // Add amount mutator to ensure proper formatting
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = is_numeric($value) ? 
            number_format((float)$value, 2, '.', '') : 0;
    }

    public function entrepreneur(): BelongsTo
    {
        return $this->belongsTo(Entrepreneur::class, 'id_entrepreneurs', 'id_entrepreneurs');
    }
}
