<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use App\Filament\Widgets\PropertyElectricityChart;
use App\Filament\Widgets\PropertyWaterChart;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            PropertyElectricityChart::make(['record' => $this->record]),
            PropertyWaterChart::make(['record' => $this->record]),
        ];
    }
}
