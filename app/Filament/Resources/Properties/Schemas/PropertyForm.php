<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->options([
                        'Villa' => 'Villa',
                        'Apartment' => 'Apartment',
                        'Building' => 'Building',
                        'Compound' => 'Compound',
                    ])
                    ->required(),
                TextInput::make('units_count')
                    ->label('Number of Units')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->maxLength(255),
                TextInput::make('electricity_number')
                    ->label('Electricity Number')
                    ->maxLength(255),
                TextInput::make('water_number')
                    ->label('Water/Elec Number')
                    ->maxLength(255),
            ]);
    }
}
