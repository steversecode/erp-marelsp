<?php

namespace App\Filament\Resources\StockLocations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockLocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('warehouse.name')
                    ->label('Warehouse')
                    ->placeholder('-'),
                TextEntry::make('parent.name')
                    ->label('Parent')
                    ->placeholder('-'),
                TextEntry::make('code'),
                TextEntry::make('barcode')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('type'),
                IconEntry::make('is_scrap')
                    ->boolean(),
                IconEntry::make('is_return')
                    ->boolean(),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('comment')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
