<?php

namespace App\Filament\Resources\StockMoves\Pages;

use App\Filament\Resources\StockMoves\StockMoveResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStockMove extends ViewRecord
{
    protected static string $resource = StockMoveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
