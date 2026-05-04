<?php

namespace App\Filament\Resources\Properties\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
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
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('value')
                    ->label('Reading Value')
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('reading_date')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('gauge')
                    ->label('Gauge')
                    ->hint('Select which gauge this reading is for')
                    ->options(function (Forms\Get $get, $livewire) {
                        $type = $get('type');
                        $property = $livewire->getOwnerRecord();

                        if (! $property || ! $type) {
                            return [];
                        }

                        $numbers = $type === 'electricity'
                            ? ($property->electricity_number ?? [])
                            : ($property->water_number ?? []);

                        if (! is_array($numbers)) {
                            return [];
                        }

                        return collect($numbers)
                            ->mapWithKeys(fn ($item) => [
                                ($item['number'] ?? '') => ($item['number'] ?? ''),
                            ])
                            ->filter()
                            ->all();
                    })
                    ->searchable()
                    ->nullable(),
                Forms\Components\Textarea::make('notes')
                    ->label('Report Issue')
                    ->hint('Optional: describe any issue with this reading')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
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
                    ->label('Reading')
                    ->numeric()
                    ->formatStateUsing(function ($state, $record) {
                        $unit = $record->type === 'electricity' ? ' (kW)' : ' (gallons)';

                        return number_format((float) $state, 2).$unit;
                    }),
                Tables\Columns\TextColumn::make('gauge')
                    ->label('Gauge')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('reading_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Report Issue')
                    ->placeholder('—')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->notes),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'electricity' => 'Electricity',
                        'water' => 'Water',
                    ])
                    ->label('Type'),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('New meter reading')
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
