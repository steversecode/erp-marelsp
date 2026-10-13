<?php

namespace Webkul\MrpReport\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Webkul\Manufacturing\Models\Order;
use Webkul\MrpReport\Services\MrpReportService;
use Webkul\Security\Models\User;

class PrintBomBenangAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'mrp_report.print.bom_benang';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mrp_report::app.reports.bom_benang.label'))
            ->icon('heroicon-o-document-chart-bar')
            ->color('gray')
            ->modalHeading(__('mrp_report::app.reports.bom_benang.modal_title'))
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
            ])
            ->action(function (Order $record, array $data) {
                return app(MrpReportService::class)->renderBomBenang([$record], $data);
            });
    }
}
