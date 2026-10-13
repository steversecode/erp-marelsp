<?php

namespace Webkul\MrpReport;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\MrpReport\Filament\Clusters\Operations\Pages\MrpReports;
use Webkul\PluginManager\Package;

class MrpReportPlugin implements Plugin
{
    public function getId(): string
    {
        return 'mrp-report';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel->pages([
            MrpReports::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
