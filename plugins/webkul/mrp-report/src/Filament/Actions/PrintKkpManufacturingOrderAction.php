<?php

namespace Webkul\MrpReport\Filament\Actions;

use Filament\Actions\Action;
use Webkul\Manufacturing\Models\Order;
use Webkul\MrpReport\Services\MrpReportService;

class PrintKkpManufacturingOrderAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'mrp_report.print.kkp_manufacturing_order';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mrp_report::app.reports.kkp_mo.label'))
            ->icon('heroicon-o-identification')
            ->color('gray')
            ->action(function (Order $record) {
                return app(MrpReportService::class)->renderKkpManufacturingOrder($record);
            });
    }
}
