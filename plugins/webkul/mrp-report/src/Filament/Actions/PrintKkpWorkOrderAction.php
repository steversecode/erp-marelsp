<?php

namespace Webkul\MrpReport\Filament\Actions;

use Filament\Actions\Action;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\MrpReport\Services\MrpReportService;

class PrintKkpWorkOrderAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'mrp_report.print.kkp_work_order';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mrp_report::app.reports.kkp_wo.label'))
            ->icon('heroicon-o-identification')
            ->color('gray')
            ->action(function (WorkOrder $record) {
                return app(MrpReportService::class)->renderKkpWorkOrder($record);
            });
    }
}
