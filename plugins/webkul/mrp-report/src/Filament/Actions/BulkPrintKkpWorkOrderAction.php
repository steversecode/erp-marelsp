<?php

namespace Webkul\MrpReport\Filament\Actions;

use Filament\Actions\BulkAction;
use Illuminate\Support\Collection;
use Webkul\MrpReport\Services\MrpReportService;

class BulkPrintKkpWorkOrderAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'mrp_report.bulk_print.kkp_work_order';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mrp_report::app.reports.kkp_wo.label'))
            ->icon('heroicon-o-identification')
            ->color('gray')
            ->action(function (Collection $records) {
                return app(MrpReportService::class)->renderBulkKkpWorkOrder($records);
            });
    }
}
