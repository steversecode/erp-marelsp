<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\PluginManager\Package;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationManufacturingOrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageManufacturingOrders extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = QuotationResource::class;

    protected static string $relationship = 'manufacturingOrders';

    protected static ?string $relatedResource = QuotationManufacturingOrderResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
            return false;
        }

        return Package::isPluginInstalled('manufacturing');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manufacturing');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        if (! Package::isPluginInstalled('manufacturing')) {
            return null;
        }

        return (string) (Livewire::current()->getRecord()->manufacturingOrders()->count() ?? 0);
    }
}
