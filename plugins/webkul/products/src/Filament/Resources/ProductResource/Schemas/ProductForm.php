<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry as Registry;
use Webkul\Product\Models\Category;
use Webkul\Support\Models\UOM;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $mainGroup = array_merge(
            [static::generalSection()],
            Registry::renderForm('left.general.after'),
            Registry::renderForm('right.settings.after'),
            [static::pricingSection()],
            Registry::hasFormSlot('left.inventory')
                ? Registry::renderForm('left.inventory')
                : [static::inventorySection()],
            [static::descriptionSection()],
            [static::mediaSection()],
            Registry::renderForm('left.append'),
            Registry::renderForm('right.append'),
        );

        return $schema
            ->components(array_merge([
                Group::make()
                    ->schema($mainGroup)
                    ->columnSpanFull(),
            ], Registry::renderForm('hidden')))
            ->columns(1);
    }

    public static function generalSection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.general.title'))
            ->schema([
                TextInput::make('name')
                    ->label(__('products::filament/resources/product.form.sections.general.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->placeholder(__('products::filament/resources/product.form.sections.general.fields.name-placeholder'))
                    ->extraInputAttributes(['style' => 'font-size: 1.25rem; font-weight: 600; height: 3.25rem;'])
                    ->columnSpanFull(),

                Grid::make(['default' => 2, 'sm' => 2, 'md' => 4])
                    ->schema([
                        Checkbox::make('sales_ok')
                            ->label(__('Can be Sold'))
                            ->default(true)
                            ->afterStateHydrated(function (Checkbox $component, $state, $record) {
                                if ($record) {
                                    $component->state((bool) ($record->sales_ok ?? $record->enable_sales ?? true));
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => (bool) $state),

                        Checkbox::make('purchase_ok')
                            ->label(__('Can be Purchased'))
                            ->default(true)
                            ->afterStateHydrated(function (Checkbox $component, $state, $record) {
                                if ($record) {
                                    $component->state((bool) ($record->purchase_ok ?? $record->enable_purchase ?? true));
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => (bool) $state),
                    ])
                    ->columnSpanFull(),

                Grid::make(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->schema([
                        Radio::make('type')
                            ->label(__('products::filament/resources/product.form.sections.settings.fields.type'))
                            ->options(ProductType::class)
                            ->default(ProductType::GOODS->value)
                            ->inline()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ProductType|string|null $state): void {
                                $defaultUomId = ProductResource::getDefaultUomIdByProductType($state);

                                if (! $defaultUomId) {
                                    return;
                                }

                                $set('uom_id', $defaultUomId);
                                $set('uom_po_id', $defaultUomId);
                            }),

                        Select::make('category_id')
                            ->label(__('products::filament/resources/product.form.sections.settings.fields.category'))
                            ->required()
                            ->relationship('category', 'full_name')
                            ->searchable()
                            ->preload()
                            ->default(Category::first()?->id)
                            ->createOptionForm(fn (Schema $schema): Schema => CategoryResource::form($schema)),

                        Select::make('company_id')
                            ->label(__('products::filament/resources/product.form.sections.settings.fields.company'))
                            ->relationship(
                                'company',
                                'name',
                                modifyQueryUsing: fn (Builder $query, $state) => $query->withTrashed()
                                    ->where(hide_deleted_unless_selected($state)),
                            )
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                            })
                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                            ->placeholder(__('products::filament/resources/product.form.sections.settings.fields.company-placeholder'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                clear_foreign_company_values(
                                    $set,
                                    $get,
                                    Registry::companyDependentFieldsFor(),
                                    $state,
                                );

                                foreach (Registry::companyDefaultFieldsFor() as $field => $resolveDefault) {
                                    $set($field, $resolveDefault($state));
                                }
                            }),

                        TextInput::make('reference')
                            ->label(__('products::filament/resources/product.form.sections.settings.fields.reference'))
                            ->placeholder('SKU / Reference')
                            ->maxLength(255),

                        TextInput::make('barcode')
                            ->label(__('products::filament/resources/product.form.sections.settings.fields.barcode'))
                            ->placeholder('Barcode / EAN')
                            ->maxLength(255),

                        Select::make('tags')
                            ->label(__('products::filament/resources/product.form.sections.general.fields.tags'))
                            ->relationship(name: 'tags', titleAttribute: 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('products_tags'),
                            ]),
                    ]),
            ]);
    }

    public static function descriptionSection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.general.fields.description'))
            ->schema([
                RichEditor::make('description')
                    ->hiddenLabel(),
            ])
            ->collapsible();
    }

    public static function mediaSection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.images.title'))
            ->schema([
                FileUpload::make('images')
                    ->image()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/bmp',
                        'image/webp',
                    ])
                    ->multiple()
                    ->storeFileNamesIn('products'),
            ])
            ->collapsible();
    }

    public static function inventorySection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.inventory.title'))
            ->schema([
                Fieldset::make(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        TextInput::make('weight')
                            ->label(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.fields.weight'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('volume')
                            ->label(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.fields.volume'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999),
                    ])
                    ->columns(2),
            ])
            ->visible(fn (Get $get): bool => $get('type') == ProductType::GOODS);
    }

    public static function settingsSection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.settings.title'))
            ->schema([
                Radio::make('type')
                    ->label(__('products::filament/resources/product.form.sections.settings.fields.type'))
                    ->options(ProductType::class)
                    ->default(ProductType::GOODS->value)
                    ->inline()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ProductType|string|null $state): void {
                        $defaultUomId = ProductResource::getDefaultUomIdByProductType($state);

                        if (! $defaultUomId) {
                            return;
                        }

                        $set('uom_id', $defaultUomId);
                        $set('uom_po_id', $defaultUomId);
                    }),
                TextInput::make('reference')
                    ->label(__('products::filament/resources/product.form.sections.settings.fields.reference'))
                    ->maxLength(255),
                TextInput::make('barcode')
                    ->label(__('products::filament/resources/product.form.sections.settings.fields.barcode'))
                    ->maxLength(255),
                Select::make('category_id')
                    ->label(__('products::filament/resources/product.form.sections.settings.fields.category'))
                    ->required()
                    ->relationship('category', 'full_name')
                    ->searchable()
                    ->preload()
                    ->default(Category::first()?->id)
                    ->createOptionForm(fn (Schema $schema): Schema => CategoryResource::form($schema)),
                Select::make('company_id')
                    ->label(__('products::filament/resources/product.form.sections.settings.fields.company'))
                    ->relationship(
                        'company',
                        'name',
                        modifyQueryUsing: fn (Builder $query, $state) => $query->withTrashed()
                            ->where(hide_deleted_unless_selected($state)),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                    ->placeholder(__('products::filament/resources/product.form.sections.settings.fields.company-placeholder'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                        clear_foreign_company_values(
                            $set,
                            $get,
                            Registry::companyDependentFieldsFor(),
                            $state,
                        );

                        foreach (Registry::companyDefaultFieldsFor() as $field => $resolveDefault) {
                            $set($field, $resolveDefault($state));
                        }
                    }),
            ]);
    }

    public static function pricingSection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.pricing.title'))
            ->schema(array_merge([
                FusedGroup::make([
                    TextInput::make('price')
                        ->numeric()
                        ->required()
                        ->default(0.00)
                        ->minValue(0)
                        ->columnSpan(2),
                    Select::make('uom_id')
                        ->placeholder(__('products::filament/resources/product.form.sections.pricing.fields.uom-placeholder'))
                        ->native(false)
                        ->required()
                        ->options(UOM::pluck('name', 'id'))
                        ->default(fn (Get $get): ?int => ProductResource::getDefaultUomIdByProductType($get('type')))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('uom_po_id', $state)),
                ])
                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.price'))
                    ->columns(3),
                FusedGroup::make([
                    TextInput::make('cost')
                        ->numeric()
                        ->default(0.00)
                        ->minValue(0)
                        ->columnSpan(2),
                    Select::make('uom_po_id')
                        ->placeholder(__('products::filament/resources/product.form.sections.pricing.fields.uom-placeholder'))
                        ->native(false)
                        ->required()
                        ->options(UOM::pluck('name', 'id'))
                        ->default(fn (Get $get): ?int => ProductResource::getDefaultUomIdByProductType($get('type')))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('uom_id', $state)),
                ])
                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.cost'))
                    ->columns(3),
            ], Registry::renderForm('right.pricing.fields')))
            ->columns(['default' => 1, 'md' => 2]);
    }
}
