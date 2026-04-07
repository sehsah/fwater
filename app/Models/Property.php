<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'units_count',
        'apartments_count',
        'people_per_apartment',
        'elevators_count',
        'ac_units_count',
        'water_filters_count',
        'address',
        'location',
        'electricity_number',
        'water_number',
        'electric_rate',
        'water_rate',
    ];

    protected $casts = [
        'electricity_number' => 'array',
        'water_number' => 'array',
    ];

    public function readings(): HasMany
    {
        return $this->hasMany(MeterReading::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
