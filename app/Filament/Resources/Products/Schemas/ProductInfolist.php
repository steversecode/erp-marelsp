<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code'),
                TextEntry::make('barcode')
                    ->placeholder('-'),
                TextEntry::make('name'),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('type'),
                IconEntry::make('track_inventory')
                    ->boolean(),
                TextEntry::make('category_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('responsible.name')
                    ->label('Responsible')
                    ->placeholder('-'),
                TextEntry::make('tracking'),
                IconEntry::make('allow_negative_stock')
                    ->boolean(),
                IconEntry::make('route_buy')
                    ->boolean(),
                IconEntry::make('route_manufacture')
                    ->boolean(),
                IconEntry::make('route_subcontract')
                    ->boolean(),
                IconEntry::make('route_mto')
                    ->boolean(),
                TextEntry::make('route_resupply_ids')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('warehouse.name')
                    ->label('Warehouse')
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->placeholder('-'),
                TextEntry::make('uom'),
                TextEntry::make('uom_po')
                    ->placeholder('-'),
                TextEntry::make('min_stock')
                    ->numeric(),
                TextEntry::make('max_stock')
                    ->numeric(),
                TextEntry::make('reorder_qty')
                    ->numeric(),
                TextEntry::make('weight')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('volume')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('lead_time_days')
                    ->numeric(),
                TextEntry::make('purchase_lead_time_days')
                    ->numeric(),
                TextEntry::make('preferred_vendor')
                    ->placeholder('-'),
                TextEntry::make('vendor_code')
                    ->placeholder('-'),
                TextEntry::make('cost_price')
                    ->money(),
                TextEntry::make('sale_price')
                    ->money(),
                TextEntry::make('invoicing_policy'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('purchase_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('costing_method'),
                TextEntry::make('valuation_method'),
                TextEntry::make('internal_notes')
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
                IconEntry::make('can_be_sold')
                    ->boolean(),
                IconEntry::make('can_be_purchased')
                    ->boolean(),
                IconEntry::make('can_be_manufactured')
                    ->boolean(),
                IconEntry::make('can_be_subcontracted')
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
