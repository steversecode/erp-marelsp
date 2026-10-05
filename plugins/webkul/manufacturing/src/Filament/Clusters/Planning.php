<?php

namespace Webkul\Manufacturing\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Support\Enums\NavigationGroup;

class Planning extends Cluster
{
    protected static ?string $slug = 'manufacturing/planning';

    protected static ?int $navigationSort = 2;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/planning.navigation.title');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('manufacturing::filament/clusters/planning.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Manufacturing;
    }
}
