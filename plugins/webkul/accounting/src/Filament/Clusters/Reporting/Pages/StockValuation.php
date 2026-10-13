<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Accounting\Filament\Clusters\Reporting;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\StockValuationExport;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Product\Models\Category;

class StockValuation extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected string $view = 'accounting::filament.clusters.reporting.pages.stock-valuation';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/reporting.pages.stock-valuation.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/reporting.pages.stock-valuation.navigation.title');
    }

    public function getTitle(): string
    {
        return __('accounting::filament/clusters/reporting.pages.stock-valuation.navigation.title');
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return true;
        }

        return true;
    }

    public function getCurrencyCode(): string
    {
        return current_company()?->currency?->code ?? config('app.currency', 'IDR');
    }

    public function getStats(): array
    {
        try {
            $query = method_exists($this, 'getFilteredTableQuery')
                ? clone $this->getFilteredTableQuery()
                : $this->getTableQuery();
        } catch (\Throwable) {
            $query = $this->getTableQuery();
        }

        $totalValuation = (float) (clone $query)
            ->selectRaw('SUM(inventories_product_quantities.quantity * COALESCE(products_products.cost, 0)) as aggregate')
            ->value('aggregate') ?: 0.0;

        $totalUnits = (float) (clone $query)->sum('inventories_product_quantities.quantity') ?: 0.0;

        $totalProducts = (int) (clone $query)
            ->distinct('inventories_product_quantities.product_id')
            ->count('inventories_product_quantities.product_id') ?: 0;

        return [
            'total_valuation' => $totalValuation,
            'total_units'     => $totalUnits,
            'total_products'  => $totalProducts,
            'currency_code'   => $this->getCurrencyCode(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.actions.export-excel'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    try {
                        $query = method_exists($this, 'getFilteredTableQuery')
                            ? clone $this->getFilteredTableQuery()
                            : $this->getTableQuery();
                    } catch (\Throwable) {
                        $query = $this->getTableQuery();
                    }

                    $records = $query->get();

                    return Excel::download(
                        new StockValuationExport($records, $this->getCurrencyCode()),
                        'stock-valuation-'.now()->format('Y-m-d').'.xlsx'
                    );
                }),

            Action::make('pdf')
                ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.actions.export-pdf'))
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->action(function () {
                    try {
                        $query = method_exists($this, 'getFilteredTableQuery')
                            ? clone $this->getFilteredTableQuery()
                            : $this->getTableQuery();
                    } catch (\Throwable) {
                        $query = $this->getTableQuery();
                    }

                    $records = $query->get();
                    $stats = $this->getStats();

                    $pdf = Pdf::loadView('accounting::filament.clusters.reporting.pages.pdfs.stock-valuation', [
                        'records'       => $records,
                        'stats'         => $stats,
                        'currency_code' => $this->getCurrencyCode(),
                        'date'          => now()->format('d M Y H:i'),
                    ])->setPaper('a4', 'landscape');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, 'stock-valuation-'.now()->format('Y-m-d').'.pdf');
                }),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return ProductQuantity::query()
            ->select('inventories_product_quantities.*')
            ->leftJoin('products_products', 'products_products.id', '=', 'inventories_product_quantities.product_id')
            ->with([
                'product.category',
                'product.uom',
                'location.warehouse',
            ])
            ->whereHas('location', function (Builder $query): void {
                $query->where('type', LocationType::INTERNAL)
                    ->where('is_scrap', false);
            })
            ->where('inventories_product_quantities.quantity', '>', 0);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->recordTitleAttribute('product.name')
            ->groups([
                Group::make('product.category.name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.category')),
                Group::make('location.warehouse.name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.warehouse')),
                Group::make('location.full_name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.location')),
                Group::make('product.name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.product')),
            ])
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.product'))
                    ->description(fn (ProductQuantity $record): ?string => $record->product?->reference ? 'SKU: '.$record->product->reference : null)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('product', function (Builder $q) use ($search): void {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('reference', 'like', "%{$search}%")
                                ->orWhere('barcode', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('products_products.name', $direction);
                    })
                    ->wrap(),

                TextColumn::make('product.category.name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.category'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('location.warehouse.name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.warehouse'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('location.full_name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.location'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('quantity')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.on-hand'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignEnd()
                    ->summarize(
                        Sum::make()
                            ->label(__('accounting::filament/clusters/reporting.common.total'))
                    ),

                TextColumn::make('product.uom.name')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.uom'))
                    ->placeholder('—')
                    ->alignCenter(),

                TextColumn::make('unit_cost')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.unit-cost'))
                    ->state(fn (ProductQuantity $record): float => (float) ($record->product?->cost ?? 0))
                    ->money(fn (): string => $this->getCurrencyCode())
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('products_products.cost', $direction);
                    })
                    ->alignEnd(),

                TextColumn::make('total_value')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.columns.total-value'))
                    ->state(fn (ProductQuantity $record): float => (float) $record->quantity * (float) ($record->product?->cost ?? 0))
                    ->money(fn (): string => $this->getCurrencyCode())
                    ->weight(FontWeight::Bold)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('(inventories_product_quantities.quantity * COALESCE(products_products.cost, 0)) '.$direction);
                    })
                    ->alignEnd()
                    ->summarize(
                        Sum::make()
                            ->label(__('accounting::filament/clusters/reporting.common.total'))
                            ->using(function ($query): float {
                                return (float) (clone $query)->selectRaw('SUM(inventories_product_quantities.quantity * COALESCE(products_products.cost, 0)) as aggregate')->value('aggregate');
                            })
                            ->money(fn (): string => $this->getCurrencyCode())
                    ),
            ])
            ->filters([
                SelectFilter::make('warehouse')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.filters.warehouse'))
                    ->options(fn (): array => Warehouse::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['values'])) {
                            return $query;
                        }

                        return $query->whereHas('location', function (Builder $q) use ($data): void {
                            $q->whereIn('warehouse_id', $data['values']);
                        });
                    }),

                SelectFilter::make('location')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.filters.location'))
                    ->relationship('location', 'full_name', fn (Builder $q) => $q->where('type', LocationType::INTERNAL))
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('category')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.filters.product-category'))
                    ->options(fn (): array => Category::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['values'])) {
                            return $query;
                        }

                        return $query->whereHas('product', function (Builder $q) use ($data): void {
                            $q->whereIn('category_id', $data['values']);
                        });
                    }),

                TernaryFilter::make('cost_status')
                    ->label(__('accounting::filament/clusters/reporting.pages.stock-valuation.filters.cost-status'))
                    ->trueLabel(__('accounting::filament/clusters/reporting.pages.stock-valuation.filters.with-cost'))
                    ->falseLabel(__('accounting::filament/clusters/reporting.pages.stock-valuation.filters.zero-cost'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->where('products_products.cost', '>', 0),
                        false: fn (Builder $query): Builder => $query->where(function (Builder $q): void {
                            $q->whereNull('products_products.cost')->orWhere('products_products.cost', '<=', 0);
                        }),
                        blank: fn (Builder $query): Builder => $query,
                    ),
            ]);
    }
}
