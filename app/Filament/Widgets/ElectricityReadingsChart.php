<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class ElectricityReadingsChart extends ChartWidget
{
    protected ?string $heading = 'Electricity Readings';

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
                    ->where('type', 'electricity'),
            ])
            ->get();

        $labels = $properties->pluck('name')->toArray();
        $aggregation = $this->filters['aggregation'] ?? 'avg';

        $data = $properties
            ->map(function ($property) use ($aggregation): float {
                $readings = $property->readings;

                if ($readings->isEmpty()) {
                    return 0.0;
                }

                if ($aggregation === 'sum') {
                    return (float) $readings->sum('value');
                }

                return round((float) $readings->avg('value'), 2);
            })
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => $aggregation === 'sum' ? 'Electricity (Total)' : 'Electricity (Average)',
                    'data' => $data,
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function updatedFilters(): void
    {
        $this->cachedData = null;
        $this->updateChartData();
    }
}
