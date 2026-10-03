<?php

namespace Webkul\SaleProductPack;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Sale\Models\OrderLine;
use Webkul\SaleProductPack\Observers\OrderLinePackObserver;

class SaleProductPackServiceProvider extends PackageServiceProvider
{
    public static string $name = 'sale-product-packs';

    public static string $viewNamespace = 'sale_product_packs';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->icon('sale-product-packs')
            ->isExtra()
            ->hasMigrations([
                '2026_10_03_000003_add_pack_columns_to_sales_order_lines_table',
            ])
            ->runsMigrations()
            ->hasDependencies(['sales', 'product-packs'])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->runsMigrations();
            });
    }

    public function packageBooted(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        OrderLine::resolveRelationUsing('packParentLine', function ($line) {
            return $line->belongsTo(OrderLine::class, 'pack_parent_line_id');
        });

        OrderLine::resolveRelationUsing('packChildLines', function ($line) {
            return $line->hasMany(OrderLine::class, 'pack_parent_line_id');
        });

        OrderLine::observe(OrderLinePackObserver::class);
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(SaleProductPackPlugin::make());
        });
    }
}
