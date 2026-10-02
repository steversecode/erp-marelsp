<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('barcode')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('type')
                    ->required()
                    ->default('goods'),
                Toggle::make('track_inventory')
                    ->required(),
                TextInput::make('category_id')
                    ->numeric()
                    ->default(null),
                Select::make('responsible_id')
                    ->relationship('responsible', 'name')
                    ->default(null),
                TextInput::make('tracking')
                    ->required()
                    ->default('quantity'),
                Toggle::make('allow_negative_stock')
                    ->required(),
                Toggle::make('route_buy')
                    ->required(),
                Toggle::make('route_manufacture')
                    ->required(),
                Toggle::make('route_subcontract')
                    ->required(),
                Toggle::make('route_mto')
                    ->required(),
                Textarea::make('route_resupply_ids')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->default(null),
                TextInput::make('category')
                    ->default(null),
                TextInput::make('uom')
                    ->required()
                    ->default('kg'),
                TextInput::make('uom_po')
                    ->default(null),
                TextInput::make('min_stock')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('max_stock')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('reorder_qty')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('weight')
                    ->numeric()
                    ->default(null),
                TextInput::make('volume')
                    ->numeric()
                    ->default(null),
                TextInput::make('lead_time_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('purchase_lead_time_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('preferred_vendor')
                    ->default(null),
                TextInput::make('vendor_code')
                    ->default(null),
                TextInput::make('cost_price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('sale_price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('invoicing_policy')
                    ->required()
                    ->default('ordered'),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('purchase_description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('costing_method')
                    ->required()
                    ->default('average'),
                TextInput::make('valuation_method')
                    ->required()
                    ->default('automated'),
                Textarea::make('internal_notes')
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
                Toggle::make('can_be_sold')
                    ->required(),
                Toggle::make('can_be_purchased')
                    ->required(),
                Toggle::make('can_be_manufactured')
                    ->required(),
                Toggle::make('can_be_subcontracted')
                    ->required(),
            ]);
    }
}
