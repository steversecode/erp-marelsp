<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('address')
                    ->default(null),
                Select::make('view_location_id')
                    ->relationship('viewLocation', 'name')
                    ->default(null),
                Select::make('lot_stock_id')
                    ->relationship('lotStock', 'name')
                    ->default(null),
                TextInput::make('incoming_steps')
                    ->required()
                    ->default('1_step'),
                TextInput::make('outgoing_steps')
                    ->required()
                    ->default('1_step'),
                Toggle::make('buy_to_resupply')
                    ->required(),
                Toggle::make('manufacture_to_resupply')
                    ->required(),
                TextInput::make('manufacture_steps')
                    ->required()
                    ->default('2_steps'),
                Textarea::make('resupply_from_warehouse_ids')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('created_by')
                    ->numeric()
                    ->default(null),
                TextInput::make('updated_by')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
