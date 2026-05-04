<?php

namespace App\Filament\Widgets;

use App\Models\MeterReading;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class PropertyElectricityChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Electricity Readings';

    protected int|string|array $columnSpan = 1;

    public ?Model $record = null;

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

        $readings = MeterReading::query()
            ->where('property_id', $this->record?->id)
            ->where('type', 'electricity')
            ->whereBetween('reading_date', [$start, $end])
            ->orderBy('reading_date')
            ->get(['reading_date', 'value']);

        $labels = $readings->map(fn ($r) => $r->reading_date->format('M d'))->toArray();
        $data = $readings->map(fn ($r) => (float) $r->value)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Electricity (kW)',
                    'data' => $data,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
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
