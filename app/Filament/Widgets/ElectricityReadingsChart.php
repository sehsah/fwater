<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ElectricityReadingsChart extends ChartWidget
{
    protected ?string $heading = 'Electricity Readings';

    protected int|string|array $columnSpan = 1;

    protected function getFilters(): ?array
    {
        return [
            'day' => 'Daily',
            'week' => 'Weekly',
            'month' => 'Monthly',
            'year' => 'Yearly',
        ];
    }

    protected function getData(): array
    {
        $period = $this->filter ?? 'month';
        [$start, $end] = $this->resolveDateRange($period);

        $properties = Property::query()
            ->when(
                ! auth()->user()?->hasRole('superadmin'),
                fn ($query) => $query->whereHas('users', fn ($query) => $query->where('users.id', auth()->id()))
            )
            ->with([
                'readings' => fn ($query) => $query
                    ->where('type', 'electricity')
                    ->whereBetween('reading_date', [$start, $end]),
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
                    'label' => 'Electricity kW',
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

    private function resolveDateRange(string $period): array
    {
        $now = now();

        return match ($period) {
            'day' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
}
