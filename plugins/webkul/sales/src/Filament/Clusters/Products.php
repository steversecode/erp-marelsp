<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Support\Enums\NavigationGroup;

class Products extends Cluster
{
    protected static ?string $slug = 'sale/products';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 0;

    protected static bool $shouldRegisterSubNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/products.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }
}
