<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\ParentResourceRegistration;

class OrderManufacturingOrderResource extends QuotationManufacturingOrderResource
{
    protected static ?string $parentResource = OrderResource::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return OrderResource::asParent()
            ->relationship('manufacturingOrders');
    }
}
