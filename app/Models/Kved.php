<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kved extends Model
{
    protected $primaryKey = 'id_kved';
    
    protected $fillable = [
        'number',
        'name'
    ];

    public function entrepreneurs(): BelongsToMany
    {
        return $this->belongsToMany(Entrepreneur::class, 'kved_entrepreneurs', 'id_kved', 'id_entrepreneurs');
    }
}