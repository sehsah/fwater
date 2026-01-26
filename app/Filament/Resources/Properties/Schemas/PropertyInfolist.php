<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PropertyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', array_column($state, 'number')) : $state)
                            ->visible(fn ($record) => ! empty($record->electricity_number)),
                        TextEntry::make('water_number')
                            ->label('Water Numbers')
                            ->badge()
                            ->separator(',')
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', array_column($state, 'number')) : $state)
                            ->visible(fn ($record) => ! empty($record->water_number)),
                    ]),
            ]);
    }
}
