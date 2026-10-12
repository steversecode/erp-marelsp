<?php

namespace Webkul\ManufacturingSwitchComponents;

use Filament\Panel;
use Webkul\Manufacturing\Models\Order;
use Webkul\ManufacturingSwitchComponents\Models\ComponentSwitch;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class ManufacturingSwitchComponentsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'manufacturing-switch-components';

    public static string $viewNamespace = 'manufacturing_switch_components';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->icon('manufacturing-switch-components')
            ->isExtra()
            ->hasTranslations()
            ->hasMigrations([
                '2026_10_12_000001_create_manufacturing_component_switches_table',
            ])
            ->runsMigrations()
            ->hasDependencies(['manufacturing', 'inventories'])
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

        Order::resolveRelationUsing('componentSwitches', function ($order) {
            return $order->hasMany(ComponentSwitch::class, 'order_id');
        });
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'manufacturing_switch_components');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'manufacturing-switch-components');

        parent::boot();
    }

    public function packageRegistered(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'manufacturing_switch_components');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'manufacturing-switch-components');

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(ManufacturingSwitchComponentsPlugin::make());
        });
    }
}
