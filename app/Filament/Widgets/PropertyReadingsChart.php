<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PropertyReadingsChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Property Readings Chart';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $properties = \App\Models\Property::query()
            ->when(
                ! auth()->user()?->hasRole('superadmin'),
                fn ($query) => $query->whereHas('users', fn ($query) => $query->where('users.id', auth()->id()))
            )
            ->with('readings')
            ->get();

        $labels = $properties->pluck('name')->toArray();

        $electricityData = [];
        $waterData = [];

        foreach ($properties as $property) {
            $electricityData[] = $property->readings->where('type', 'electricity')->sum('value');
            $waterData[] = $property->readings->where('type', 'water')->sum('value');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Electricity',
                    'data' => $electricityData,
                    'backgroundColor' => '#f59e0b', // warning color
                ],
                [
                    'label' => 'Water',
                    'data' => $waterData,
                    'backgroundColor' => '#3b82f6', // primary color
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
