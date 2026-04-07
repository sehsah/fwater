<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Support\ConsumptionSummary;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ConsumptionSummaryWidget extends StatsOverviewWidget
{
    use HasFiltersSchema;

    protected string $view = 'filament.widgets.consumption-summary-widget';

    protected ?string $heading = 'Consumption summary';

    protected static ?int $sort = 1;

    public function mount(): void
    {
        $this->filters = array_merge([
            'period' => 'day',
            'startDate' => null,
            'endDate' => null,
        ], $this->filters ?? []);

        $this->getFiltersSchema()->fill($this->filters);
    }

    public function updatedFilters(): void
    {
        $this->cachedStats = null;
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('period')
                ->label('Period')
                ->options([
                    'day' => 'Today',
                    'month' => 'This month',
                    'year' => 'This year',
                    'custom' => 'Custom range',
                ])
                ->default('day')
                ->live(),
            DatePicker::make('startDate')
                ->label('Start date')
                ->visible(fn (Get $get): bool => ($get('period') ?? 'day') === 'custom')
                ->native(false)
                ->live(),
            DatePicker::make('endDate')
                ->label('End date')
                ->visible(fn (Get $get): bool => ($get('period') ?? 'day') === 'custom')
                ->native(false)
                ->live(),
        ]);
    }

    protected function getStats(): array
    {
        $propertyIds = Property::query()
            ->when(
                ! auth()->user()?->hasRole('superadmin'),
                fn ($query) => $query->whereHas('users', fn ($query) => $query->where('users.id', auth()->id()))
            )
            ->pluck('id')
            ->all();

        $period = $this->filters['period'] ?? 'day';
        [$start, $end] = $this->resolveDateRange();

        $summary = ConsumptionSummary::forPropertyIdsInDateRange($propertyIds, $start, $end);
        $rangeLabel = $this->formatRangeLabel($start, $end, $period);

        return [
            Stat::make('Total buildings', number_format(count($propertyIds))),
            Stat::make('Water', $this->formatStatValue($summary['water']))
                ->description($rangeLabel),
            Stat::make('Electricity', $this->formatStatValue($summary['electricity']))
                ->description($rangeLabel),
        ];
    }

    /**
     * @param  array{total: float, average_per_day: float}  $slice
     */
    private function formatStatValue(array $slice): string
    {
        return 'Total: '.$this->formatNumber($slice['total']).' | Avg/day: '.$this->formatNumber($slice['average_per_day']);
    }

    private function formatNumber(float $value): string
    {
        return number_format($value, 2);
    }

    /**
     * @return array{0: \Illuminate\Support\Carbon, 1: \Illuminate\Support\Carbon}
     */
    private function resolveDateRange(): array
    {
        $period = $this->filters['period'] ?? 'day';
        $now = now();

        return match ($period) {
            'day' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'month' => [$now->copy()->startOfMonth()->startOfDay(), $now->copy()->endOfMonth()->endOfDay()],
            'year' => [$now->copy()->startOfYear()->startOfDay(), $now->copy()->endOfYear()->endOfDay()],
            'custom' => $this->resolveCustomRange($now),
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveCustomRange(Carbon $now): array
    {
        $startRaw = $this->filters['startDate'] ?? null;
        $endRaw = $this->filters['endDate'] ?? null;

        $start = $startRaw ? Carbon::parse($startRaw)->startOfDay() : $now->copy()->startOfDay();
        $end = $endRaw ? Carbon::parse($endRaw)->endOfDay() : $now->copy()->endOfDay();

        if ($end->lessThan($start)) {
            return [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    private function formatRangeLabel(Carbon $start, Carbon $end, string $period): string
    {
        return match ($period) {
            'day' => $start->format('M j, Y'),
            'month' => $start->format('F Y'),
            'year' => $start->format('Y'),
            default => $start->format('M j, Y').' – '.$end->format('M j, Y'),
        };
    }
}
