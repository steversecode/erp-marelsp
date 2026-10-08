<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderManufacturingOrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageManufacturingOrders as BaseManageManufacturingOrders;

class ManageManufacturingOrders extends BaseManageManufacturingOrders
{
    protected static string $resource = OrderResource::class;

    protected static ?string $relatedResource = OrderManufacturingOrderResource::class;
}
