<?php

namespace App\Filament\Resources\Properties\Schemas;

use App\Support\ConsumptionSummary;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PropertyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Summary ملخص')
                    ->schema([
                        TextEntry::make('summary_total_buildings')
                            ->label('Total buildings')
                            ->state(fn () => number_format(1)),
                        TextEntry::make('summary_water_day')
                            ->label('Water / Day')
                            ->state(fn ($record) => self::summaryValue($record, 'water', 'day')),
                        TextEntry::make('summary_water_week')
                            ->label('Water / Week')
                            ->state(fn ($record) => self::summaryValue($record, 'water', 'week')),
                        TextEntry::make('summary_water_month')
                            ->label('Water / Month')
                            ->state(fn ($record) => self::summaryValue($record, 'water', 'month')),
                        TextEntry::make('summary_water_year')
                            ->label('Water / Year')
                            ->state(fn ($record) => self::summaryValue($record, 'water', 'year')),
                        TextEntry::make('summary_electricity_day')
                            ->label('Electricity / Day')
                            ->state(fn ($record) => self::summaryValue($record, 'electricity', 'day')),
                        TextEntry::make('summary_electricity_week')
                            ->label('Electricity / Week')
                            ->state(fn ($record) => self::summaryValue($record, 'electricity', 'week')),
                        TextEntry::make('summary_electricity_month')
                            ->label('Electricity / Month')
                            ->state(fn ($record) => self::summaryValue($record, 'electricity', 'month')),
                        TextEntry::make('summary_electricity_year')
                            ->label('Electricity / Year')
                            ->state(fn ($record) => self::summaryValue($record, 'electricity', 'year')),
                    ])
                    ->columns(3),
                Section::make('Property Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Property Name'),
                        TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Villa' => 'success',
                                'Apartment' => 'info',
                                'Building' => 'warning',
                                'Compound' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('units_count')
                            ->label('Number of Units')
                            ->numeric(),
                        TextEntry::make('apartments_count')
                            ->label('Number of Apartments')
                            ->numeric()
                            ->visible(fn ($record) => ! is_null($record->apartments_count)),
                        TextEntry::make('people_per_apartment')
                            ->label('People per Apartment')
                            ->numeric()
                            ->visible(fn ($record) => ! is_null($record->people_per_apartment)),
                        TextEntry::make('elevators_count')
                            ->label('Number of Elevators')
                            ->numeric()
                            ->visible(fn ($record) => ! is_null($record->elevators_count)),
                        TextEntry::make('ac_units_count')
                            ->label('Number of AC Units')
                            ->numeric()
                            ->visible(fn ($record) => ! is_null($record->ac_units_count)),
                        TextEntry::make('water_filters_count')
                            ->label('Number of Water Filters')
                            ->numeric()
                            ->visible(fn ($record) => ! is_null($record->water_filters_count)),
                        TextEntry::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                        TextEntry::make('location')
                            ->label('Location'),
                    ]),
                Section::make('Contact Numbers')
                    ->schema([
                        TextEntry::make('electricity_number')
                            ->label('Electricity Numbers')
                            ->badge()
                            ->separator(',')
                            ->formatStateUsing(fn ($state) => is_array($state)
                                ? implode(', ', array_map(
                                    fn ($item) => ($item['number'] ?? '').' kilo watt',
                                    $state
                                ))
                                : $state)
                            ->visible(fn ($record) => ! empty($record->electricity_number)),
                        TextEntry::make('water_number')
                            ->label('Water Numbers')
                            ->badge()
                            ->separator(',')
                            ->formatStateUsing(fn ($state) => is_array($state)
                                ? implode(', ', array_map(
                                    fn ($item) => ($item['number'] ?? '').' gallons',
                                    $state
                                ))
                                : $state)
                            ->visible(fn ($record) => ! empty($record->water_number)),
                    ]),
            ]);
    }

    private static function summaryValue($record, string $type, string $period): string
    {
        $summary = ConsumptionSummary::forProperty((int) $record->id);
        $total = $summary[$type]['total'][$period] ?? 0.0;
        $average = $summary[$type]['average'][$period] ?? 0.0;

        return 'Total: '.self::formatNumber($total).' | Avg: '.self::formatNumber($average);
    }

    private static function formatNumber(float $value): string
    {
        return number_format($value, 2);
    }
}
