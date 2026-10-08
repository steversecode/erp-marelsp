<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Support\Enums\NavigationGroup;

class ToInvoice extends Cluster
{
    protected static ?string $slug = 'sale/invoice';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/to-invoice.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }
}
