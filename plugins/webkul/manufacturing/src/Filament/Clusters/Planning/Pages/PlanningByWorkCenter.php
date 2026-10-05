<?php

namespace Webkul\Manufacturing\Filament\Clusters\Planning\Pages;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Planning;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Models\WorkOrder;

class PlanningByWorkCenter extends Page
{
    protected static ?string $cluster = Planning::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?int $navigationSort = 1;

    protected string $view = 'manufacturing::filament.clusters.planning.pages.planning-by-work-center';

    public string $viewMode = 'week'; // 'day', 'week', 'month'

    public string $currentDate = '';

    public string $statusFilter = 'all';

    public ?int $workCenterFilter = null;

    public string $search = '';

    public ?int $selectedWorkOrderId = null;

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/planning.pages.planning-by-work-center.navigation.title');
    }

    public function getTitle(): string
    {
        return __('manufacturing::filament/clusters/planning.pages.planning-by-work-center.title');
    }

    public function mount(): void
    {
        $this->currentDate = now()->toDateString();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['day', 'week', 'month'])) {
            $this->viewMode = $mode;
        }
    }

    public function previous(): void
    {
        $date = Carbon::parse($this->currentDate ?: now());

        $this->currentDate = match ($this->viewMode) {
            'day'   => $date->subDay()->toDateString(),
            'week'  => $date->subWeek()->toDateString(),
            'month' => $date->subMonth()->toDateString(),
            default => $date->subWeek()->toDateString(),
        };
    }

    public function next(): void
    {
        $date = Carbon::parse($this->currentDate ?: now());

        $this->currentDate = match ($this->viewMode) {
            'day'   => $date->addDay()->toDateString(),
            'week'  => $date->addWeek()->toDateString(),
            'month' => $date->addMonth()->toDateString(),
            default => $date->addWeek()->toDateString(),
        };
    }

    public function today(): void
    {
        $this->currentDate = now()->toDateString();
    }

    public function openWorkOrderModal(int $id): void
    {
        $this->selectedWorkOrderId = $id;
    }

    public function closeWorkOrderModal(): void
    {
        $this->selectedWorkOrderId = null;
    }

    public function startWorkOrder(int $id): void
    {
        try {
            $workOrder = WorkOrder::findOrFail($id);
            $workOrder->start();

            Notification::make()
                ->title('Work Order Started')
                ->body("Work order {$workOrder->name} has started.")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Action Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function finishWorkOrder(int $id): void
    {
        try {
            $workOrder = WorkOrder::findOrFail($id);
            $workOrder->finish();

            Notification::make()
                ->title('Work Order Finished')
                ->body("Work order {$workOrder->name} has been marked as finished.")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Action Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    #[Computed]
    public function selectedWorkOrder(): ?WorkOrder
    {
        if (! $this->selectedWorkOrderId) {
            return null;
        }

        return WorkOrder::with([
            'workCenter',
            'manufacturingOrder.product',
            'manufacturingOrder.assignedUser',
            'operation',
            'product.uom',
            'productivityLogs.assignedUser',
        ])->find($this->selectedWorkOrderId);
    }

    #[Computed]
    public function timelineData(): array
    {
        $current = Carbon::parse($this->currentDate ?: now()->toDateString());

        [$rangeStart, $rangeEnd, $columns] = match ($this->viewMode) {
            'day' => $this->buildDayColumns($current),
            'week' => $this->buildWeekColumns($current),
            'month' => $this->buildMonthColumns($current),
            default => $this->buildWeekColumns($current),
        };

        $rangeStartTs = $rangeStart->timestamp;
        $rangeEndTs = $rangeEnd->timestamp;
        $totalSeconds = max(1, $rangeEndTs - $rangeStartTs);

        // Work centers
        $workCentersQuery = WorkCenter::query()
            ->where('company_id', current_company_id());

        if ($this->workCenterFilter) {
            $workCentersQuery->where('id', $this->workCenterFilter);
        }

        $workCenters = $workCentersQuery->orderBy('sort')->get();

        // Work orders
        $workOrdersQuery = WorkOrder::query()
            ->with([
                'workCenter',
                'manufacturingOrder.product.uom',
                'operation',
            ])
            ->whereHas('manufacturingOrder', fn ($q) => $q->where('company_id', current_company_id()));

        if ($this->statusFilter !== 'all') {
            $workOrdersQuery->where('state', $this->statusFilter);
        }

        if ($this->search) {
            $search = $this->search;
            $workOrdersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('manufacturingOrder', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                    });
            });
        }

        $allWorkOrders = $workOrdersQuery->get();

        $rows = [];
        $totalOrdersCount = 0;
        $inProgressCount = 0;
        $readyCount = 0;
        $doneCount = 0;
        $totalPlannedHours = 0.0;

        foreach ($workCenters as $wc) {
            $wcOrders = $allWorkOrders->where('work_center_id', $wc->id);
            $items = [];
            $wcPlannedMinutes = 0;

            foreach ($wcOrders as $wo) {
                $woStart = $wo->started_at ?? $wo->manufacturingOrder?->started_at ?? now();
                $expectedDuration = (float) ($wo->expected_duration ?: 60);
                $woEnd = $wo->finished_at ?? (clone $woStart)->addMinutes($expectedDuration);

                $startTs = $woStart->timestamp;
                $endTs = $woEnd->timestamp;

                if ($endTs <= $startTs) {
                    $endTs = $startTs + max(1800, (int) ($expectedDuration * 60));
                }

                // Check overlap
                if ($endTs < $rangeStartTs || $startTs > $rangeEndTs) {
                    continue;
                }

                $totalOrdersCount++;
                $stateVal = $wo->state instanceof WorkOrderState ? $wo->state->value : (string) $wo->state;

                if ($stateVal === WorkOrderState::PROGRESS->value) {
                    $inProgressCount++;
                } elseif ($stateVal === WorkOrderState::READY->value) {
                    $readyCount++;
                } elseif ($stateVal === WorkOrderState::DONE->value) {
                    $doneCount++;
                }

                $wcPlannedMinutes += $expectedDuration;
                $totalPlannedHours += ($expectedDuration / 60.0);

                // Clamped coordinates
                $clampedStartTs = max($rangeStartTs, $startTs);
                $clampedEndTs = min($rangeEndTs, $endTs);

                $leftPercent = (($clampedStartTs - $rangeStartTs) / $totalSeconds) * 100;
                $widthPercent = (($clampedEndTs - $clampedStartTs) / $totalSeconds) * 100;

                $leftPercent = max(0, min(97.0, $leftPercent));
                $widthPercent = max(3.0, min(100 - $leftPercent, $widthPercent));

                $items[] = [
                    'id'               => $wo->id,
                    'name'             => $wo->name,
                    'mo_id'            => $wo->manufacturing_order_id,
                    'mo_name'          => $wo->manufacturingOrder?->name ?? '—',
                    'product_name'     => $wo->manufacturingOrder?->product?->name ?? '—',
                    'quantity'         => (float) ($wo->quantity_produced ?: $wo->manufacturingOrder?->quantity ?: 0),
                    'uom'              => $wo->manufacturingOrder?->product?->uom?->name ?? '',
                    'state'            => $stateVal,
                    'state_label'      => $wo->state instanceof WorkOrderState ? $wo->state->getLabel() : ucfirst($stateVal),
                    'color_theme'      => $this->getStatusColorTheme($stateVal),
                    'start_formatted'  => $woStart->format('d M H:i'),
                    'end_formatted'    => $woEnd->format('d M H:i'),
                    'duration_hours'   => round($expectedDuration / 60, 1),
                    'left_percent'     => round($leftPercent, 2),
                    'width_percent'    => round($widthPercent, 2),
                    'is_in_progress'   => $stateVal === WorkOrderState::PROGRESS->value,
                    'is_ready'         => $stateVal === WorkOrderState::READY->value,
                    'is_done'          => $stateVal === WorkOrderState::DONE->value,
                ];
            }

            $rows[] = [
                'work_center'     => $wc,
                'items'           => $items,
                'planned_hours'   => round($wcPlannedMinutes / 60, 1),
                'active_orders'   => count($items),
            ];
        }

        $periodTitle = match ($this->viewMode) {
            'day'   => $current->format('l, d F Y'),
            'week'  => 'Week ' . $current->isoWeek() . ' (' . $rangeStart->format('d M') . ' - ' . $rangeEnd->format('d M Y') . ')',
            'month' => $current->format('F Y'),
            default => $current->format('F Y'),
        };

        return [
            'period_title'        => $periodTitle,
            'range_start'         => $rangeStart,
            'range_end'           => $rangeEnd,
            'columns'             => $columns,
            'rows'                => $rows,
            'stats'               => [
                'total_orders'    => $totalOrdersCount,
                'in_progress'     => $inProgressCount,
                'ready'           => $readyCount,
                'done'            => $doneCount,
                'planned_hours'   => round($totalPlannedHours, 1),
                'work_centers'    => count($workCenters),
            ],
        ];
    }

    protected function buildDayColumns(Carbon $date): array
    {
        $start = (clone $date)->startOfDay();
        $end = (clone $date)->endOfDay();
        $columns = [];

        for ($h = 0; $h < 24; $h++) {
            $slotTime = (clone $start)->addHours($h);
            $columns[] = [
                'label'    => $slotTime->format('H:i'),
                'sublabel' => $h % 4 === 0 ? $slotTime->format('A') : '',
                'is_today' => $date->isToday() && now()->hour === $h,
            ];
        }

        return [$start, $end, $columns];
    }

    protected function buildWeekColumns(Carbon $date): array
    {
        $start = (clone $date)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end = (clone $date)->endOfWeek(Carbon::SUNDAY)->endOfDay();
        $columns = [];

        for ($d = 0; $d < 7; $d++) {
            $day = (clone $start)->addDays($d);
            $columns[] = [
                'label'    => $day->format('D'),
                'sublabel' => $day->format('d M'),
                'is_today' => $day->isToday(),
            ];
        }

        return [$start, $end, $columns];
    }

    protected function buildMonthColumns(Carbon $date): array
    {
        $start = (clone $date)->startOfMonth()->startOfDay();
        $end = (clone $date)->endOfMonth()->endOfDay();
        $daysInMonth = $date->daysInMonth;
        $columns = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $day = (clone $start)->day($d);
            $columns[] = [
                'label'    => (string) $d,
                'sublabel' => $day->format('D')[0],
                'is_today' => $day->isToday(),
            ];
        }

        return [$start, $end, $columns];
    }

    protected function getStatusColorTheme(string $state): array
    {
        return match ($state) {
            WorkOrderState::PROGRESS->value => [
                'bg'     => 'bg-amber-600 hover:bg-amber-500',
                'border' => 'border-amber-400',
                'text'   => 'text-white',
                'badge'  => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
            ],
            WorkOrderState::READY->value => [
                'bg'     => 'bg-blue-600 hover:bg-blue-500',
                'border' => 'border-blue-400',
                'text'   => 'text-white',
                'badge'  => 'bg-blue-500/15 text-blue-400 border border-blue-500/30',
            ],
            WorkOrderState::DONE->value => [
                'bg'     => 'bg-emerald-600 hover:bg-emerald-500',
                'border' => 'border-emerald-400',
                'text'   => 'text-white',
                'badge'  => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
            ],
            WorkOrderState::CANCEL->value => [
                'bg'     => 'bg-rose-600 hover:bg-rose-500',
                'border' => 'border-rose-400',
                'text'   => 'text-white',
                'badge'  => 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
            ],
            default => [
                'bg'     => 'bg-slate-600 hover:bg-slate-500',
                'border' => 'border-slate-500',
                'text'   => 'text-white',
                'badge'  => 'bg-slate-500/15 text-slate-400 border border-slate-500/30',
            ],
        };
    }
}
