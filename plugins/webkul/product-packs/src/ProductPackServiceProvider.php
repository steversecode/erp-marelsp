<?php

namespace Webkul\ProductPack;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Product\Models\Product;
use Webkul\ProductPack\Filament\Resources\ProductResource\Schemas\ProductPackSchema;
use Webkul\ProductPack\Models\ProductPackLine;

class ProductPackServiceProvider extends PackageServiceProvider
{
    public static string $name = 'product-packs';

    public static string $viewNamespace = 'product_packs';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasMigrations([
                '2026_10_03_000001_add_pack_fields_to_products_products_table',
                '2026_10_03_000002_create_products_product_pack_lines_table',
            ])
            ->runsMigrations()
            ->hasDependencies(['products'])
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

        Product::contributeFillable([
            'is_pack',
            'pack_type',
            'pack_component_price',
            'pack_modifiable',
        ]);

        Product::contributeCasts([
            'is_pack'         => 'boolean',
            'pack_modifiable' => 'boolean',
        ]);

        Product::resolveRelationUsing('packLines', function ($product) {
            return $product->hasMany(ProductPackLine::class, 'parent_product_id');
        });

        Product::resolveRelationUsing('usedInPackLines', function ($product) {
            return $product->hasMany(ProductPackLine::class, 'product_id');
        });

        ProductSchemaRegistry::form('left.append', fn () => ProductPackSchema::packSection());
        ProductSchemaRegistry::table('columns', fn () => ProductPackSchema::tableColumns());
        ProductSchemaRegistry::table('filters', fn () => ProductPackSchema::tableFilters());
        ProductSchemaRegistry::eagerLoad(['packLines', 'packLines.product']);
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(ProductPackPlugin::make());
        });
    }
}
