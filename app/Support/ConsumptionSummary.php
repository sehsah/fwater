<?php

namespace App\Support;

use App\Models\MeterReading;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ConsumptionSummary
{
    /**
     * @var array<string, array<string, array<string, array<string, float>>>>
     */
    private static array $cache = [];

    /**
     * @param  array<int>  $propertyIds
     * @return array<string, array<string, array<string, float>>>
     */
    public static function forPropertyIds(array $propertyIds): array
    {
        $propertyIds = array_values(array_filter($propertyIds, fn ($id) => ! is_null($id)));
        sort($propertyIds);

        $key = 'properties:'.implode(',', $propertyIds);

        return self::remember($key, function () use ($propertyIds): array {
            if (empty($propertyIds)) {
                return self::emptySummary();
            }

            $readings = MeterReading::query()
                ->whereIn('property_id', $propertyIds)
                ->get(['type', 'reading_date', 'value']);

            return self::buildSummary($readings);
        });
    }

    /**
     * @return array<string, array<string, array<string, float>>>
     */
    public static function forProperty(int $propertyId): array
    {
        $key = 'property:'.$propertyId;

        return self::remember($key, function () use ($propertyId): array {
            $readings = MeterReading::query()
                ->where('property_id', $propertyId)
                ->get(['type', 'reading_date', 'value']);

            return self::buildSummary($readings);
        });
    }

    /**
     * Consumption for a calendar date range (inclusive). Not cached — safe for dashboard filters.
     *
     * @param  array<int>  $propertyIds
     * @return array<string, array{total: float, average_per_day: float}>
     */
    public static function forPropertyIdsInDateRange(array $propertyIds, Carbon $rangeStart, Carbon $rangeEnd): array
    {
        $propertyIds = array_values(array_filter($propertyIds, fn ($id) => ! is_null($id)));
        sort($propertyIds);

        if (empty($propertyIds)) {
            return [
                'water' => ['total' => 0.0, 'average_per_day' => 0.0],
                'electricity' => ['total' => 0.0, 'average_per_day' => 0.0],
            ];
        }

        $start = $rangeStart->copy()->startOfDay();
        $end = $rangeEnd->copy()->endOfDay();

        $readings = MeterReading::query()
            ->whereIn('property_id', $propertyIds)
            ->whereDate('reading_date', '>=', $start)
            ->whereDate('reading_date', '<=', $end)
            ->get(['type', 'reading_date', 'value']);

        $readings = $readings->filter(fn (MeterReading $reading) => ! is_null($reading->reading_date));

        $inclusiveDays = max(1, (int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1);

        return [
            'water' => self::buildTypeSummaryForRange($readings->where('type', 'water'), $inclusiveDays),
            'electricity' => self::buildTypeSummaryForRange($readings->where('type', 'electricity'), $inclusiveDays),
        ];
    }

    /**
     * @param  Collection<int, MeterReading>  $readings
     * @return array<string, array<string, array<string, float>>>
     */
    private static function buildSummary(Collection $readings): array
    {
        $readings = $readings->filter(fn (MeterReading $reading) => ! is_null($reading->reading_date));

        return [
            'water' => self::buildTypeSummary($readings->where('type', 'water')),
            'electricity' => self::buildTypeSummary($readings->where('type', 'electricity')),
        ];
    }

    /**
     * @param  Collection<int, MeterReading>  $readings
     * @return array<string, array<string, float>>
     */
    private static function buildTypeSummary(Collection $readings): array
    {
        if ($readings->isEmpty()) {
            return self::emptyTypeSummary();
        }

        $now = now();

        $totals = [
            'day' => self::sumSince($readings, $now->copy()->subDay()->startOfDay()),
            'week' => self::sumSince($readings, $now->copy()->subDays(7)->startOfDay()),
            'month' => self::sumSince($readings, $now->copy()->subDays(30)->startOfDay()),
            'year' => self::sumSince($readings, $now->copy()->subDays(365)->startOfDay()),
        ];

        $averages = [
            'day' => self::averageBy($readings, 'day'),
            'week' => self::averageBy($readings, 'week'),
            'month' => self::averageBy($readings, 'month'),
            'year' => self::averageBy($readings, 'year'),
        ];

        return [
            'total' => $totals,
            'average' => $averages,
        ];
    }

    /**
     * @param  Collection<int, MeterReading>  $readings
     */
    private static function sumSince(Collection $readings, Carbon $from): float
    {
        return (float) $readings
            ->filter(fn (MeterReading $reading) => $reading->reading_date->greaterThanOrEqualTo($from))
            ->sum('value');
    }

    /**
     * @param  Collection<int, MeterReading>  $readings
     */
    private static function averageBy(Collection $readings, string $period): float
    {
        if ($readings->isEmpty()) {
            return 0.0;
        }

        $groups = $readings->groupBy(fn (MeterReading $reading) => self::periodKey($reading->reading_date, $period));

        $totals = $groups->map(fn (Collection $group) => (float) $group->sum('value'));

        return (float) $totals->avg();
    }

    private static function periodKey(Carbon $date, string $period): string
    {
        return match ($period) {
            'day' => $date->format('Y-m-d'),
            'week' => $date->isoWeekYear.'-W'.str_pad((string) $date->isoWeek, 2, '0', STR_PAD_LEFT),
            'month' => $date->format('Y-m'),
            'year' => $date->format('Y'),
            default => $date->format('Y-m-d'),
        };
    }

    /**
     * @return array<string, array<string, array<string, float>>>
     */
    private static function emptySummary(): array
    {
        return [
            'water' => self::emptyTypeSummary(),
            'electricity' => self::emptyTypeSummary(),
        ];
    }

    /**
     * @return array<string, array<string, float>>
     */
    private static function emptyTypeSummary(): array
    {
        return [
            'total' => [
                'day' => 0.0,
                'week' => 0.0,
                'month' => 0.0,
                'year' => 0.0,
            ],
            'average' => [
                'day' => 0.0,
                'week' => 0.0,
                'month' => 0.0,
                'year' => 0.0,
            ],
        ];
    }

    /**
     * @param  Collection<int, MeterReading>  $readings
     * @return array{total: float, average_per_day: float}
     */
    private static function buildTypeSummaryForRange(Collection $readings, int $inclusiveDays): array
    {
        $total = (float) $readings->sum('value');

        return [
            'total' => $total,
            'average_per_day' => $total / $inclusiveDays,
        ];
    }

    /**
     * @template T
     *
     * @param  callable():T  $callback
     * @return T
     */
    private static function remember(string $key, callable $callback)
    {
        if (! array_key_exists($key, self::$cache)) {
            self::$cache[$key] = $callback();
        }

        return self::$cache[$key];
    }
}
