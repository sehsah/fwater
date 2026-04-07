<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->live()
                    ->required(),
                TextInput::make('apartments_count')
                    ->label('Number of Apartments')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn ($get): bool => in_array($get('type'), ['Building', 'Compound'])),
                TextInput::make('people_per_apartment')
                    ->label('People per Apartment')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn ($get): bool => in_array($get('type'), ['Building', 'Compound'])),
                TextInput::make('elevators_count')
                    ->label('Number of Elevators')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn ($get): bool => in_array($get('type'), ['Building', 'Compound'])),
                TextInput::make('ac_units_count')
                    ->label('Number of AC Units')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn ($get): bool => in_array($get('type'), ['Building', 'Compound'])),
                TextInput::make('water_filters_count')
                    ->label('Number of Water Filters')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn ($get): bool => in_array($get('type'), ['Building', 'Compound'])),
                Repeater::make('units')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Unit Name/Number')
                            ->required(),
                        TextInput::make('type')
                            ->label('Type (e.g. 2BHK)'),
                        Select::make('is_occupied')
                            ->label('Status')
                            ->options([
                                0 => 'Vacant',
                                1 => 'Occupied',
                            ])
                            ->default(0),
                        TextInput::make('number_of_conditioning')
                            ->label('Number of Conditioning'),
                        TextInput::make('number_of_people')
                            ->label('Number of People'),
                        TextInput::make('number_of_rooms')
                            ->label('Number of Rooms'),
                        TextInput::make('electricity_number')
                            ->label('Electricity Number')
                            ->suffix('kilo watt'),
                        TextInput::make('water_number')
                            ->label('Water Number')
                            ->suffix('gallons'),
                        TextInput::make('description')
                            ->label('Notes'),
                    ])
                    ->visible(fn ($get): bool => in_array($get('type'), ['Building', 'Compound']))
                    ->columnSpanFull()
                    ->addActionLabel('Add Unit'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('electric_rate')
                        ->label('Electricity Rate')
                        ->numeric()
                        ->minValue(0),
                TextInput::make('water_rate')
                        ->label('Water Rate')
                        ->numeric()
                        ->minValue(0),
                TextInput::make('location')
                    ->maxLength(255),
                Repeater::make('electricity_number')
                    ->label('Electricity Numbers')
                    ->schema([
                        TextInput::make('number'),
                    ])
                    ->addActionLabel('Add Electricity Number')
                    ->columnSpanFull(),
                Repeater::make('water_number')
                    ->label('Water Numbers')
                    ->schema([
                        TextInput::make('number'),
                    ])
                    ->addActionLabel('Add Water Number')
                    ->columnSpanFull(),
            ]);
    }
}
