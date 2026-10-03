<?php

namespace Webkul\ProductPack\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Product\Models\Product;
use Webkul\ProductPack\Enums\PackComponentPrice;
use Webkul\ProductPack\Enums\PackType;

class ProductPackSchema
{
    public static function packSection(): Section
    {
        return Section::make(__('product_packs::filament/resources/product.pack_section.title'))
            ->description(__('product_packs::filament/resources/product.pack_section.description'))
            ->icon('heroicon-o-archive-box')
            ->schema([
                Toggle::make('is_pack')
                    ->label(__('product_packs::filament/resources/product.fields.is_pack'))
                    ->helperText(__('product_packs::filament/resources/product.fields.is_pack_helper'))
                    ->live()
                    ->default(false),

                Grid::make(['default' => 1, 'md' => 3])
                    ->visible(fn (Get $get): bool => (bool) $get('is_pack'))
                    ->schema([
                        Select::make('pack_type')
                            ->label(__('product_packs::filament/resources/product.fields.pack_type'))
                            ->options(PackType::class)
                            ->default(PackType::DETAILED->value)
                            ->required()
                            ->live()
                            ->helperText(__('product_packs::filament/resources/product.fields.pack_type_helper')),

                        Select::make('pack_component_price')
                            ->label(__('product_packs::filament/resources/product.fields.pack_component_price'))
                            ->options(PackComponentPrice::class)
                            ->default(PackComponentPrice::DETAILED->value)
                            ->required()
                            ->visible(fn (Get $get): bool => $get('pack_type') === PackType::DETAILED->value)
                            ->live()
                            ->helperText(__('product_packs::filament/resources/product.fields.pack_component_price_helper')),

                        Toggle::make('pack_modifiable')
                            ->label(__('product_packs::filament/resources/product.fields.pack_modifiable'))
                            ->visible(fn (Get $get): bool => $get('pack_type') === PackType::DETAILED->value && $get('pack_component_price') === PackComponentPrice::DETAILED->value)
                            ->helperText(__('product_packs::filament/resources/product.fields.pack_modifiable_helper'))
                            ->default(false),
                    ]),

                Repeater::make('packLines')
                    ->relationship('packLines')
                    ->label(__('product_packs::filament/resources/product.fields.pack_lines'))
                    ->visible(fn (Get $get): bool => (bool) $get('is_pack'))
                    ->columns(['default' => 1, 'sm' => 12])
                    ->schema([
                        Select::make('product_id')
                            ->label(__('product_packs::filament/resources/product.fields.component_product'))
                            ->relationship(
                                'product',
                                'name',
                                modifyQueryUsing: fn (Builder $query, $record) => $query
                                    ->when($record?->id, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->columnSpan(['default' => 12, 'sm' => 6]),

                        TextInput::make('quantity')
                            ->label(__('product_packs::filament/resources/product.fields.quantity'))
                            ->numeric()
                            ->default(1)
                            ->minValue(0.0001)
                            ->required()
                            ->live()
                            ->columnSpan(['default' => 6, 'sm' => 3]),

                        Placeholder::make('unit_price')
                            ->label(__('product_packs::filament/resources/product.fields.unit_price'))
                            ->content(function (Get $get): string {
                                $productId = $get('product_id');
                                if (! filled($productId)) {
                                    return '—';
                                }
                                $product = Product::find($productId);

                                return money($product?->price ?? 0);
                            })
                            ->columnSpan(['default' => 6, 'sm' => 3]),
                    ])
                    ->addActionLabel(__('product_packs::filament/resources/product.actions.add_component'))
                    ->reorderable(false)
                    ->collapsible()
                    ->defaultItems(0),
            ]);
    }

    public static function tableColumns(): array
    {
        return [
            IconColumn::make('is_pack')
                ->label(__('product_packs::filament/resources/product.fields.is_pack_short'))
                ->boolean()
                ->trueIcon('heroicon-o-archive-box')
                ->falseIcon('heroicon-o-minus')
                ->trueColor('primary')
                ->falseColor('gray')
                ->toggleable(),
        ];
    }

    public static function tableFilters(): array
    {
        return [
            TernaryFilter::make('is_pack')
                ->label(__('product_packs::filament/resources/product.fields.is_pack')),
        ];
    }
}
