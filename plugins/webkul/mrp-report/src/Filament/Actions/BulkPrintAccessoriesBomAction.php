<?php

namespace Webkul\MrpReport\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use Webkul\MrpReport\Services\MrpReportService;
use Webkul\Security\Models\User;

class BulkPrintAccessoriesBomAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'mrp_report.bulk_print.accessories_bom';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mrp_report::app.reports.accessories_bom.label'))
            ->icon('heroicon-o-document-chart-bar')
            ->color('gray')
            ->modalHeading(__('mrp_report::app.reports.accessories_bom.modal_title'))
            ->modalSubmitActionLabel(__('mrp_report::app.actions.print'))
            ->form([
                Select::make('mengajukan_id')
                    ->label(__('mrp_report::app.signatures.mengajukan'))
                    ->options(fn () => User::query()->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->preload(),
                Select::make('mengetahui_1_id')
                    ->label(__('mrp_report::app.signatures.purchasing'))
                    ->options(fn () => User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Select::make('mengetahui_2_id')
                    ->label(__('mrp_report::app.signatures.gudang'))
                    ->options(fn () => User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Select::make('mengetahui_3_id')
                    ->label(__('mrp_report::app.signatures.gm'))
                    ->options(fn () => User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
            ])
            ->action(function (Collection $records, array $data) {
                return app(MrpReportService::class)->renderAccessoriesBom($records, $data);
            });
    }
}
