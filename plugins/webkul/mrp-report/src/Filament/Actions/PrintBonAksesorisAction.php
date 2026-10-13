<?php

namespace Webkul\MrpReport\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Webkul\Manufacturing\Models\Order;
use Webkul\MrpReport\Services\MrpReportService;
use Webkul\Security\Models\User;

class PrintBonAksesorisAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'mrp_report.print.bon_aksesoris';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mrp_report::app.reports.bon_aksesoris.label'))
            ->icon('heroicon-o-clipboard-document-check')
            ->color('gray')
            ->modalHeading(__('mrp_report::app.reports.bon_aksesoris.modal_title'))
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
            ])
            ->action(function (Order $record, array $data) {
                return app(MrpReportService::class)->renderBonAksesoris([$record], $data);
            });
    }
}
