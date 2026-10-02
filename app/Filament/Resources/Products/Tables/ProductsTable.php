<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('barcode')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                ImageColumn::make('image'),
                TextColumn::make('type')
                    ->searchable(),
                IconColumn::make('track_inventory')
                    ->boolean(),
                TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('responsible.name')
                    ->searchable(),
                TextColumn::make('tracking')
                    ->searchable(),
                IconColumn::make('allow_negative_stock')
                    ->boolean(),
                IconColumn::make('route_buy')
                    ->boolean(),
                IconColumn::make('route_manufacture')
                    ->boolean(),
                IconColumn::make('route_subcontract')
                    ->boolean(),
                IconColumn::make('route_mto')
                    ->boolean(),
                TextColumn::make('warehouse.name')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('uom')
                    ->searchable(),
                TextColumn::make('uom_po')
                    ->searchable(),
                TextColumn::make('min_stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reorder_qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('volume')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lead_time_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('purchase_lead_time_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('preferred_vendor')
                    ->searchable(),
                TextColumn::make('vendor_code')
                    ->searchable(),
                TextColumn::make('cost_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('invoicing_policy')
                    ->searchable(),
                TextColumn::make('costing_method')
                    ->searchable(),
                TextColumn::make('valuation_method')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('can_be_sold')
                    ->boolean(),
                IconColumn::make('can_be_purchased')
                    ->boolean(),
                IconColumn::make('can_be_manufactured')
                    ->boolean(),
                IconColumn::make('can_be_subcontracted')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
