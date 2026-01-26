<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    /** @use HasFactory<\Database\Factories\UnitFactory> */
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'type',
        'is_occupied',
        'number_of_conditioning',
        'number_of_people',
        'number_of_rooms',
        'description',
        'electricity_number',
        'water_number',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
