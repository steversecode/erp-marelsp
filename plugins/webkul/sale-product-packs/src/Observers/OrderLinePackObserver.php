<?php

namespace Webkul\SaleProductPack\Observers;

use Webkul\Sale\Models\OrderLine;
use Webkul\SaleProductPack\Services\SalePackManager;

class OrderLinePackObserver
{
    public function saved(OrderLine $orderLine): void
    {
        SalePackManager::handleOrderLineSaved($orderLine);
    }

    public function deleted(OrderLine $orderLine): void
    {
        SalePackManager::handleOrderLineDeleted($orderLine);
    }
}
