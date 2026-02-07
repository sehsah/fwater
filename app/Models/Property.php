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
        'address',
        'location',
        'electricity_number',
        'water_number',
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
