<?php

namespace App\Filament\Resources\ManufacturingOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ManufacturingOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('mo_number')
                    ->required(),
                Select::make('bom_id')
                    ->relationship('bom', 'name')
                    ->required(),
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                TextInput::make('planned_qty')
                    ->required()
                    ->numeric(),
                TextInput::make('produced_qty')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('uom')
                    ->required()
                    ->default('pcs'),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'confirmed' => 'Confirmed',
            'in_progress' => 'In progress',
            'done' => 'Done',
            'cancelled' => 'Cancelled',
        ])
                    ->default('draft')
                    ->required(),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                Select::make('source_location_id')
                    ->relationship('sourceLocation', 'name')
                    ->default(null),
                Select::make('destination_location_id')
                    ->relationship('destinationLocation', 'name')
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
