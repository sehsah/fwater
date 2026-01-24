<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeterReading extends Model
{
    /** @use HasFactory<\Database\Factories\MeterReadingFactory> */
    use HasFactory;

    protected $fillable = [
        'property_id',
        'type',
        'value',
        'reading_date',
    ];

    protected $casts = [
        'reading_date' => 'date',
        'value' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
