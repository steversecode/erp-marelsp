<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Support\Enums\NavigationGroup;

class Configuration extends Cluster
{
    protected static ?string $slug = 'sale/configurations';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }
}
