<?php

namespace App\Filament\Resources\StockMoves\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockMoveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('move_number')
                    ->required(),
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Select::make('from_location_id')
                    ->relationship('fromLocation', 'name')
                    ->required(),
                Select::make('to_location_id')
                    ->relationship('toLocation', 'name')
                    ->required(),
                TextInput::make('qty')
                    ->required()
                    ->numeric(),
                TextInput::make('uom')
                    ->required()
                    ->default('kg'),
                Select::make('category')
                    ->options([
            'po_receipt' => 'Po receipt',
            'production_return' => 'Production return',
            'mo_consumption' => 'Mo consumption',
            'mo_finished_goods' => 'Mo finished goods',
            'sample_issue' => 'Sample issue',
            'dyeing_out' => 'Dyeing out',
            'dyeing_return' => 'Dyeing return',
            'adjustment' => 'Adjustment',
        ])
                    ->required(),
                TextInput::make('reference_type')
                    ->default(null),
                TextInput::make('reference_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('reference_number')
                    ->default(null),
                TextInput::make('batch_lot_number')
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
