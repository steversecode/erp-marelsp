<?php

namespace App\Filament\Resources\ManufacturingOrders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ManufacturingOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('mo_number'),
                TextEntry::make('bom.name')
                    ->label('Bom'),
                TextEntry::make('product.name')
                    ->label('Product'),
                TextEntry::make('planned_qty')
                    ->numeric(),
                TextEntry::make('produced_qty')
                    ->numeric(),
                TextEntry::make('uom'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('sourceLocation.name')
                    ->label('Source location')
                    ->placeholder('-'),
                TextEntry::make('destinationLocation.name')
                    ->label('Destination location')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
