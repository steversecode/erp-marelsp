<?php

namespace App\Filament\Resources\StockMoves\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockMoveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('move_number'),
                TextEntry::make('product.name')
                    ->label('Product'),
                TextEntry::make('fromLocation.name')
                    ->label('From location'),
                TextEntry::make('toLocation.name')
                    ->label('To location'),
                TextEntry::make('qty')
                    ->numeric(),
                TextEntry::make('uom'),
                TextEntry::make('category')
                    ->badge(),
                TextEntry::make('reference_type')
                    ->placeholder('-'),
                TextEntry::make('reference_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('reference_number')
                    ->placeholder('-'),
                TextEntry::make('batch_lot_number')
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
