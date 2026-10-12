<?php

namespace Webkul\ManufacturingSwitchComponents;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class ManufacturingSwitchComponentsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'manufacturing-switch-components';
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
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
