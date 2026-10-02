<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WarehouseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('code'),
                TextEntry::make('address')
                    ->placeholder('-'),
                TextEntry::make('viewLocation.name')
                    ->label('View location')
                    ->placeholder('-'),
                TextEntry::make('lotStock.name')
                    ->label('Lot stock')
                    ->placeholder('-'),
                TextEntry::make('incoming_steps'),
                TextEntry::make('outgoing_steps'),
                IconEntry::make('buy_to_resupply')
                    ->boolean(),
                IconEntry::make('manufacture_to_resupply')
                    ->boolean(),
                TextEntry::make('manufacture_steps'),
                TextEntry::make('resupply_from_warehouse_ids')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('updated_by')
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
