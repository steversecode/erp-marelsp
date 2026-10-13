<?php

namespace Webkul\MrpReport\Filament\Clusters\Operations\Pages;

use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\MrpReport\Services\MrpReportService;
use Webkul\Security\Models\User;

class MrpReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $cluster = Operations::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?int $navigationSort = 10;

    protected string $view = 'mrp_report::filament.clusters.operations.pages.mrp-reports';

    public ?string $report_type = 'bom_benang';

    public array $manufacturing_order_ids = [];

    public array $work_order_ids = [];

    public ?int $mengajukan_id = null;

    public ?int $mengetahui_1_id = null;

    public ?int $mengetahui_2_id = null;

    public ?int $mengetahui_3_id = null;

    public function mount(): void
    {
        $this->mengajukan_id = auth()->id();
        $this->form->fill([
            'report_type'             => 'bom_benang',
            'manufacturing_order_ids' => [],
            'work_order_ids'          => [],
            'mengajukan_id'           => auth()->id(),
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('mrp_report::app.navigation.reports');
    }

    public function getTitle(): string
    {
        return __('mrp_report::app.navigation.reports');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('mrp_report::app.navigation.reports'))
                    ->description(__('mrp_report::app.plugin.description'))
                    ->schema([
                        Radio::make('report_type')
                            ->label('Tipe Laporan')
                            ->options([
                                'bom_benang'           => __('mrp_report::app.reports.bom_benang.title').' — '.__('mrp_report::app.reports.bom_benang.description'),
                                'accessories_bom'      => __('mrp_report::app.reports.accessories_bom.title').' — '.__('mrp_report::app.reports.accessories_bom.description'),
                                'bon_aksesoris'        => __('mrp_report::app.reports.bon_aksesoris.title').' — '.__('mrp_report::app.reports.bon_aksesoris.description'),
                                'kkp_mo'               => __('mrp_report::app.reports.kkp_mo.title').' — '.__('mrp_report::app.reports.kkp_mo.description'),
                                'kkp_wo'               => __('mrp_report::app.reports.kkp_wo.title').' — '.__('mrp_report::app.reports.kkp_wo.description'),
                                'production_order_msp' => __('mrp_report::app.reports.production_order_msp.title').' — '.__('mrp_report::app.reports.production_order_msp.description'),
                            ])
                            ->required()
                            ->live(),

                        Select::make('manufacturing_order_ids')
                            ->label('Manufacturing Orders')
                            ->placeholder('Pilih satu atau lebih Manufacturing Order...')
                            ->options(fn () => Order::query()
                                ->with('product')
                                ->latest('id')
                                ->limit(200)
                                ->get()
                                ->mapWithKeys(fn (Order $order) => [
                                    $order->id => "{$order->name} — ".($order->product?->name ?? 'No Product'),
                                ]))
                            ->searchable()
                            ->multiple()
                            ->preload()
                            ->visible(fn ($get) => $get('report_type') !== 'kkp_wo')
                            ->required(fn ($get) => $get('report_type') !== 'kkp_wo'),

                        Select::make('work_order_ids')
                            ->label('Work Orders')
                            ->placeholder('Pilih satu atau lebih Work Order...')
                            ->options(fn () => WorkOrder::query()
                                ->with(['manufacturingOrder', 'operation'])
                                ->latest('id')
                                ->limit(200)
                                ->get()
                                ->mapWithKeys(fn (WorkOrder $wo) => [
                                    $wo->id => "{$wo->name} — ".($wo->operation?->name ?? 'Op').' ('.($wo->manufacturingOrder?->name ?? 'MO').')',
                                ]))
                            ->searchable()
                            ->multiple()
                            ->preload()
                            ->visible(fn ($get) => $get('report_type') === 'kkp_wo')
                            ->required(fn ($get) => $get('report_type') === 'kkp_wo'),
                    ]),

                Section::make('Tanda Tangan & Otorisasi')
                    ->description('Pilih pejabat yang bertanda tangan pada laporan')
                    ->visible(fn ($get) => in_array($get('report_type'), ['bom_benang', 'accessories_bom', 'bon_aksesoris', 'production_order_msp']))
                    ->columns(2)
                    ->schema([
                        Select::make('mengajukan_id')
                            ->label(__('mrp_report::app.signatures.mengajukan'))
                            ->options(fn () => User::query()->pluck('name', 'id'))
                            ->default(fn () => auth()->id())
                            ->searchable()
                            ->preload(),

                        Select::make('mengetahui_1_id')
                            ->label(fn ($get) => match ($get('report_type')) {
                                'production_order_msp' => __('mrp_report::app.signatures.gudang').' (Diperiksa Oleh)',
                                default                => __('mrp_report::app.signatures.purchasing'),
                            })
                            ->options(fn () => User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),

                        Select::make('mengetahui_2_id')
                            ->label(fn ($get) => match ($get('report_type')) {
                                'production_order_msp' => __('mrp_report::app.signatures.gm').' (Approved By)',
                                default                => __('mrp_report::app.signatures.gudang'),
                            })
                            ->options(fn () => User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => in_array($get('report_type'), ['bom_benang', 'accessories_bom', 'production_order_msp'])),

                        Select::make('mengetahui_3_id')
                            ->label(__('mrp_report::app.signatures.gm'))
                            ->options(fn () => User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('report_type') === 'accessories_bom'),
                    ]),
            ]);
    }

    public function generateReport(): ?StreamedResponse
    {
        $data = $this->form->getState();
        $service = app(MrpReportService::class);

        $reportType = $data['report_type'] ?? 'bom_benang';

        if ($reportType === 'kkp_wo') {
            $woIds = $data['work_order_ids'] ?? [];
            if (empty($woIds)) {
                Notification::make()->title('Silakan pilih minimal 1 Work Order.')->danger()->send();

                return null;
            }

            $workOrders = WorkOrder::query()->whereIn('id', $woIds)->get();

            return $service->renderBulkKkpWorkOrder($workOrders);
        }

        $moIds = $data['manufacturing_order_ids'] ?? [];
        if (empty($moIds)) {
            Notification::make()->title('Silakan pilih minimal 1 Manufacturing Order.')->danger()->send();

            return null;
        }

        $orders = Order::query()->whereIn('id', $moIds)->get();

        return match ($reportType) {
            'bom_benang'           => $service->renderBomBenang($orders, $data),
            'accessories_bom'      => $service->renderAccessoriesBom($orders, $data),
            'bon_aksesoris'        => $service->renderBonAksesoris($orders, $data),
            'kkp_mo'               => $service->renderBulkKkpManufacturingOrder($orders),
            'production_order_msp' => $service->renderProductionOrderMsp($orders, $data),
            default                => null,
        };
    }
}
