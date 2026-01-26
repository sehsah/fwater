<?php

namespace App\Filament\Resources\Properties\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReadingsRelationManager extends RelationManager
{
    protected static string $relationship = 'readings';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->can('view_reading', \App\Models\MeterReading::class) ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('type')
                    ->options([
                        'electricity' => 'Electricity',
                        'water' => 'Water',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->label('Reading Value')
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('reading_date')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => 'water',
                        'warning' => 'electricity',
                    ]),
                Tables\Columns\TextColumn::make('value')
                    ->numeric(),
                Tables\Columns\TextColumn::make('reading_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->authorize(fn () => auth()->user()?->can('create_reading', \App\Models\MeterReading::class)),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->authorize(fn ($record) => auth()->user()?->can('update_reading', $record)),
                Actions\DeleteAction::make()
                    ->authorize(fn ($record) => auth()->user()?->can('delete_reading', $record)),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->authorize(fn () => auth()->user()?->can('delete_reading', \App\Models\MeterReading::class)),
                ]),
            ]);
    }
}
