<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                        'Duplex' => 'Duplex',
                        'Studio' => 'Studio',
                    ])
                    ->live()
                    ->required(),
                Repeater::make('units')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Unit Name/Number')
                            ->required(),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'residential' => 'Residential',
                                'commercial' => 'Commercial',
                                'industrial' => 'Industrial',
                                'agricultural' => 'Agricultural',
                                'other' => 'Other',
                            ]),
                        TextInput::make('number_of_conditioning')
                            ->label('Number of Conditioning')
                            ->numeric()
                            ->required(),
                        TextInput::make('number_of_people')
                            ->label('Number of People')
                            ->numeric()
                            ->required(),
                        TextInput::make('number_of_rooms')
                            ->label('Number of Rooms')
                            ->numeric()
                            ->required(),
                        Select::make('is_occupied')
                            ->label('Status')
                            ->options([
                                0 => 'Vacant',
                                1 => 'Occupied',
                            ])
                            ->default(0),
                        TextInput::make('description')
                            ->label('Notes'),
                    ])
                    ->visible(fn ( $get): bool => in_array($get('type'), ['Building', 'Compound']))
                    ->columnSpanFull()
                    ->addActionLabel('Add Unit'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->maxLength(255),
                Repeater::make('electricity_number')
                    ->label('Electricity Numbers')
                    ->simple(
                        TextInput::make('number')->required(),
                    )
                    ->addActionLabel('Add Electricity Number')
                    ->columnSpanFull(),
                Repeater::make('water_number')
                    ->label('Water Numbers')
                    ->simple(
                        TextInput::make('number')->required(),
                    )
                    ->addActionLabel('Add Water Number')
                    ->columnSpanFull(),
            ]);
    }
}
