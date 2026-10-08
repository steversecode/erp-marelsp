<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateQuotation extends CreateRecord
{
    use HandlesCrossCompanyException;
    use HasRepeaterColumnManager;

    protected static string $resource = QuotationResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/orders/resources/quotation/pages/create-quotation.notification.title'))
            ->body(__('sales::filament/clusters/orders/resources/quotation/pages/create-quotation.notification.body'));
    }

    protected function afterCreate(): void
    {
        try {
            SaleOrder::computeSaleOrder($this->getRecord());
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->body($e->getMessage())
                ->send();

            $this->halt(shouldRollbackDatabaseTransaction: true);
        }
    }
}
