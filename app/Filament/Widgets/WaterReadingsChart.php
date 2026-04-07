<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class WaterReadingsChart extends ChartWidget
{
    protected ?string $heading = 'Water Readings';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {

        $properties = Property::query()
            ->when(
                ! auth()->user()?->hasRole('superadmin'),
                fn ($query) => $query->whereHas('users', fn ($query) => $query->where('users.id', auth()->id()))
            )
            ->with([
                'readings' => fn ($query) => $query
                    ->where('type', 'water'),
            ])
            ->get();

        $labels = $properties->pluck('name')->toArray();

        $data = $properties
            ->map(function ($property): float {
                $readings = $property->readings;

                if ($readings->isEmpty()) {
                    return 0.0;
                }

                return round((float) $readings->avg('value'), 2);
            })
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Water (Average)',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
