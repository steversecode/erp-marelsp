<?php

namespace Webkul\MrpReport;

use Filament\Panel;
use Illuminate\Support\Facades\Schema;
use Webkul\MrpReport\Services\MrpReportService;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class MrpReportServiceProvider extends PackageServiceProvider
{
    public static string $name = 'mrp-report';

    public static string $viewNamespace = 'mrp_report';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->icon('mrp-report')
            ->isExtra()
            ->hasTranslations()
            ->hasViews()
            ->hasDependencies(['manufacturing'])
            ->hasInstallCommand(function (InstallCommand $command) {
                // Install hooks
            });
    }

    public function packageBooted(): void
    {
        if (class_exists(Plugin::class) && Schema::hasTable('plugins')) {
            try {
                Plugin::firstOrCreate([
                    'name' => static::$name,
                ], [
                    'author'         => 'PT Marel Sukses Pratama',
                    'summary'        => 'Custom MRP Reports (KKP Work Order, KKP MO, BoM Benang, Accessories BoM, Bon Aksesoris, and Production Order MSP)',
                    'description'    => 'Comprehensive manufacturing reports for Aureus ERP',
                    'latest_version' => '1.0.0',
                    'is_active'      => true,
                    'is_installed'   => true,
                ]);
            } catch (\Throwable) {
                // Silently pass if database connection is pending
            }
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mrp_report');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mrp-report');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mrp_report');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mrp-report');

        parent::boot();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MrpReportService::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mrp_report');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mrp-report');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mrp_report');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mrp-report');

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(MrpReportPlugin::make());
        });
    }
}
