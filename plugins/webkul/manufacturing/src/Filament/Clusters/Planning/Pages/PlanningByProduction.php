<?php

namespace Webkul\Manufacturing\Filament\Clusters\Planning\Pages;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Facades\Manufacturing;
use Webkul\Manufacturing\Filament\Clusters\Planning;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Models\WorkOrder;

class PlanningByProduction extends Page
{
    protected static ?string $cluster = Planning::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    protected string $view = 'manufacturing::filament.clusters.planning.pages.planning-by-production';

    public string $viewMode = 'month'; // 'week', 'month'

    public string $currentDate = '';

    public string $statusFilter = 'all';

    public ?int $workCenterFilter = null;

    public string $search = '';

    public ?int $selectedOrderId = null;

    public ?int $selectedWorkOrderId = null;

    public array $expandedOrderIds = [];

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/planning.pages.planning-by-production.navigation.title');
    }

    public function getTitle(): string
    {
        return __('manufacturing::filament/clusters/planning.pages.planning-by-production.title');
    }

    public function mount(): void
    {
        $this->currentDate = now()->toDateString();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['week', 'month'])) {
            $this->viewMode = $mode;
        }
    }

    public function previous(): void
    {
        $date = Carbon::parse($this->currentDate ?: now());

        $this->currentDate = match ($this->viewMode) {
            'week'  => $date->subWeek()->toDateString(),
            'month' => $date->subMonth()->toDateString(),
            default => $date->subMonth()->toDateString(),
        };
    }

    public function next(): void
    {
        $date = Carbon::parse($this->currentDate ?: now());

        $this->currentDate = match ($this->viewMode) {
            'week'  => $date->addWeek()->toDateString(),
            'month' => $date->addMonth()->toDateString(),
            default => $date->addMonth()->toDateString(),
        };
    }

    public function today(): void
    {
        $this->currentDate = now()->toDateString();
    }

    public function toggleExpandOrder(int $id): void
    {
        if (in_array($id, $this->expandedOrderIds)) {
            $this->expandedOrderIds = array_values(array_diff($this->expandedOrderIds, [$id]));
        } else {
            $this->expandedOrderIds[] = $id;
        }
    }

    public function expandAll(): void
    {
        $visibleOrderIds = collect($this->timelineData['rows'])->pluck('order.id')->all();
        $this->expandedOrderIds = array_unique(array_merge($this->expandedOrderIds, $visibleOrderIds));
    }

    public function collapseAll(): void
    {
        $this->expandedOrderIds = [];
    }

    public function openOrderModal(int $id): void
    {
        $this->selectedOrderId = $id;
    }

    public function closeOrderModal(): void
    {
        $this->selectedOrderId = null;
    }

    public function openWorkOrderModal(int $id): void
    {
        $this->selectedWorkOrderId = $id;
    }

    public function closeWorkOrderModal(): void
    {
        $this->selectedWorkOrderId = null;
    }

    public function planOrder(int $id): void
    {
        try {
            $order = Order::findOrFail($id);

            Manufacturing::planManufacturingOrder($order);

            Notification::make()
                ->title(__('Order Planned Successfully'))
                ->body(__("Manufacturing order :name and its operations have been scheduled according to work center calendars.", ['name' => $order->name]))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('Planning Failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function unplanOrder(int $id): void
    {
        try {
            $order = Order::findOrFail($id);

            Manufacturing::unplanManufacturingOrder($order);

            Notification::make()
                ->title(__('Order Unplanned'))
                ->body(__("Manufacturing order :name has been unplanned and calendar slots released.", ['name' => $order->name]))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('Action Failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function planAllVisibleOrders(): void
    {
        $unplanned = collect($this->timelineData['rows'])
            ->filter(fn ($row) => ! $row['is_planned'] && in_array($row['state'], ['draft', 'confirmed']))
            ->pluck('order');

        if ($unplanned->isEmpty()) {
            Notification::make()
                ->title(__('No Unplanned Orders'))
                ->body(__('All visible confirmed orders in this period are already planned.'))
                ->info()
                ->send();

            return;
        }

        $successCount = 0;
        $errors = [];

        foreach ($unplanned as $order) {
            try {
                Manufacturing::planManufacturingOrder($order);
                $successCount++;
            } catch (\Throwable $e) {
                $errors[] = "{$order->name}: {$e->getMessage()}";
            }
        }

        if ($successCount > 0) {
            Notification::make()
                ->title(__('Batch Planning Complete'))
                ->body(__("Successfully planned :count order(s).", ['count' => $successCount]))
                ->success()
                ->send();
        }

        if (! empty($errors)) {
            Notification::make()
                ->title(__('Some Orders Could Not Be Planned'))
                ->body(implode("\n", array_slice($errors, 0, 3)))
                ->warning()
                ->send();
        }
    }

    #[Computed]
    public function selectedOrder(): ?Order
    {
        if (! $this->selectedOrderId) {
            return null;
        }

        return Order::with([
            'product.uom',
            'assignedUser',
            'workOrders.workCenter',
            'workOrders.blockedByWorkOrders',
            'workOrders.dependentWorkOrders',
            'rawMaterialMoves.product.uom',
            'finishedMoves.product.uom',
        ])->find($this->selectedOrderId);
    }

    #[Computed]
    public function selectedWorkOrder(): ?WorkOrder
    {
        if (! $this->selectedWorkOrderId) {
            return null;
        }

        return WorkOrder::with([
            'workCenter',
            'manufacturingOrder.product.uom',
            'manufacturingOrder.assignedUser',
            'operation',
            'blockedByWorkOrders',
            'dependentWorkOrders',
            'productivityLogs.assignedUser',
        ])->find($this->selectedWorkOrderId);
    }

    #[Computed]
    public function timelineData(): array
    {
        $current = Carbon::parse($this->currentDate ?: now()->toDateString());

        [$rangeStart, $rangeEnd, $columns] = match ($this->viewMode) {
            'week'  => $this->buildWeekColumns($current),
            'month' => $this->buildMonthColumns($current),
            default => $this->buildMonthColumns($current),
        };

        $rangeStartTs = $rangeStart->timestamp;
        $rangeEndTs = $rangeEnd->timestamp;
        $totalSeconds = max(1, $rangeEndTs - $rangeStartTs);

        $workCenters = WorkCenter::query()
            ->where('company_id', current_company_id())
            ->orderBy('sort')
            ->get(['id', 'name', 'code']);

        $ordersQuery = Order::query()
            ->where('company_id', current_company_id())
            ->with([
                'product.uom',
                'workOrders.workCenter',
                'workOrders.blockedByWorkOrders',
                'workOrders.dependentWorkOrders',
                'assignedUser',
            ]);

        if ($this->statusFilter !== 'all') {
            $ordersQuery->where('state', $this->statusFilter);
        }

        if ($this->workCenterFilter) {
            $ordersQuery->whereHas('workOrders', fn ($q) => $q->where('work_center_id', $this->workCenterFilter));
        }

        if ($this->search) {
            $search = $this->search;
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $allOrders = $ordersQuery->orderBy('started_at', 'desc')->get();

        $rows = [];
        $totalOrdersCount = 0;
        $inProgressCount = 0;
        $confirmedCount = 0;
        $doneCount = 0;
        $overdueCount = 0;
        $plannedCount = 0;
        $unplannedCount = 0;
        $totalQuantityProducing = 0.0;

        foreach ($allOrders as $order) {
            $moStart = $order->started_at ?? $order->created_at ?? now();
            // Default 3 days span if deadline/finished_at is not set
            $moEnd = $order->finished_at ?? $order->deadline_at ?? (clone $moStart)->addDays(3);

            $startTs = $moStart->timestamp;
            $endTs = $moEnd->timestamp;

            if ($endTs <= $startTs) {
                $endTs = $startTs + (3 * 86400);
            }

            // Check if within visible time window
            if ($endTs < $rangeStartTs || $startTs > $rangeEndTs) {
                continue;
            }

            $totalOrdersCount++;
            $stateVal = $order->state instanceof ManufacturingOrderState ? $order->state->value : (string) $order->state;

            if ($stateVal === ManufacturingOrderState::PROGRESS->value) {
                $inProgressCount++;
            } elseif ($stateVal === ManufacturingOrderState::CONFIRMED->value) {
                $confirmedCount++;
            } elseif ($stateVal === ManufacturingOrderState::DONE->value) {
                $doneCount++;
            }

            $isPlanned = (bool) $order->is_planned;
            if ($isPlanned) {
                $plannedCount++;
            } elseif (in_array($stateVal, ['draft', 'confirmed'])) {
                $unplannedCount++;
            }

            $isOverdue = $order->deadline_at
                && $order->deadline_at->lt(now())
                && ! in_array($stateVal, [ManufacturingOrderState::DONE->value, ManufacturingOrderState::CANCEL->value]);

            if ($isOverdue) {
                $overdueCount++;
            }

            $totalQuantityProducing += (float) $order->quantity;

            // Clamped coordinates in timestamps for MO bar
            $clampedStartTs = max($rangeStartTs, $startTs);
            $clampedEndTs = min($rangeEndTs, $endTs);

            $leftPercent = (($clampedStartTs - $rangeStartTs) / $totalSeconds) * 100;
            $widthPercent = (($clampedEndTs - $clampedStartTs) / $totalSeconds) * 100;

            $leftPercent = max(0, min(97.0, $leftPercent));
            $widthPercent = max(3.5, min(100 - $leftPercent, $widthPercent));

            // Work order operations mapping with dependency information
            $workOrdersCount = $order->workOrders->count();
            $doneWoCount = $order->workOrders->filter(
                fn ($wo) => ($wo->state instanceof WorkOrderState ? $wo->state->value : $wo->state) === WorkOrderState::DONE->value
            )->count();

            $progressPercent = $workOrdersCount > 0
                ? round(($doneWoCount / $workOrdersCount) * 100)
                : (float_compare($order->quantity_producing, 0) > 0 ? round(($order->quantity_producing / max($order->quantity, 1)) * 100) : 0);

            // Detailed operation bars along the timeline
            $operations = [];
            foreach ($order->workOrders->sortBy('sort') as $wo) {
                $woStart = $wo->started_at ?? $moStart;
                $expectedMin = (float) ($wo->expected_duration ?: 60);
                $woEnd = $wo->finished_at ?? (clone $woStart)->addMinutes($expectedMin);

                $woStartTs = $woStart->timestamp;
                $woEndTs = $woEnd->timestamp;
                if ($woEndTs <= $woStartTs) {
                    $woEndTs = $woStartTs + max(1800, (int) ($expectedMin * 60));
                }

                $woClampedStart = max($rangeStartTs, $woStartTs);
                $woClampedEnd = min($rangeEndTs, $woEndTs);

                $woLeftPct = (($woClampedStart - $rangeStartTs) / $totalSeconds) * 100;
                $woWidthPct = (($woClampedEnd - $woClampedStart) / $totalSeconds) * 100;

                $woStateVal = $wo->state instanceof WorkOrderState ? $wo->state->value : (string) $wo->state;

                // Dependencies status
                $blockingWos = $wo->blockedByWorkOrders->map(fn ($b) => [
                    'id'      => $b->id,
                    'name'    => $b->name,
                    'state'   => $b->state instanceof WorkOrderState ? $b->state->value : (string) $b->state,
                    'is_done' => in_array($b->state instanceof WorkOrderState ? $b->state->value : (string) $b->state, ['done', 'cancel']),
                ])->all();

                $isBlocked = collect($blockingWos)->contains(fn ($b) => ! $b['is_done']);

                $operations[] = [
                    'id'               => $wo->id,
                    'name'             => $wo->name,
                    'work_center_name' => $wo->workCenter?->name ?? 'Unassigned',
                    'work_center_code' => $wo->workCenter?->code,
                    'state'            => $woStateVal,
                    'state_label'      => $wo->state instanceof WorkOrderState ? $wo->state->getLabel() : ucfirst($woStateVal),
                    'duration_hours'   => round($expectedMin / 60, 1),
                    'actual_duration'  => round((float) $wo->duration / 60, 1),
                    'start_formatted'  => $woStart->format('d M H:i'),
                    'end_formatted'    => $woEnd->format('d M H:i'),
                    'left_percent'     => round(max(0, min(97.0, $woLeftPct)), 2),
                    'width_percent'    => round(max(2.5, min(100 - $woLeftPct, $woWidthPct)), 2),
                    'blocking_wos'     => $blockingWos,
                    'is_blocked'       => $isBlocked,
                    'bar_class'        => match ($woStateVal) {
                        'progress' => 'gantt-bar-progress',
                        'ready'    => 'gantt-bar-ready',
                        'done'     => 'gantt-bar-done',
                        'cancel'   => 'gantt-bar-overdue',
                        default    => 'gantt-bar-waiting',
                    },
                ];
            }

            $rows[] = [
                'order'              => $order,
                'state'              => $stateVal,
                'state_label'        => $order->state instanceof ManufacturingOrderState ? $order->state->getLabel() : ucfirst($stateVal),
                'color_theme'        => $this->getOrderColorTheme($stateVal, $isOverdue),
                'is_overdue'         => $isOverdue,
                'is_planned'         => $isPlanned,
                'start_formatted'    => $moStart->format('d M Y, H:i'),
                'deadline_formatted' => $order->deadline_at ? $order->deadline_at->format('d M Y, H:i') : 'No deadline',
                'left_percent'       => round($leftPercent, 2),
                'width_percent'      => round($widthPercent, 2),
                'progress_percent'   => $progressPercent,
                'work_orders_count'  => $workOrdersCount,
                'done_wo_count'      => $doneWoCount,
                'operations'         => $operations,
                'is_expanded'        => in_array($order->id, $this->expandedOrderIds),
                'bar_class'          => $isOverdue
                    ? 'gantt-bar-overdue'
                    : match ($stateVal) {
                        'progress'  => 'gantt-bar-progress',
                        'confirmed' => 'gantt-bar-confirmed',
                        'to_close'  => 'gantt-bar-ready',
                        'done'      => 'gantt-bar-done',
                        default     => 'gantt-bar-waiting',
                    },
            ];
        }

        $periodTitle = match ($this->viewMode) {
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
            'work_centers'        => $workCenters,
            'stats'               => [
                'total_orders'     => $totalOrdersCount,
                'in_progress'      => $inProgressCount,
                'confirmed'        => $confirmedCount,
                'done'             => $doneCount,
                'overdue'          => $overdueCount,
                'planned'          => $plannedCount,
                'unplanned'        => $unplannedCount,
                'total_qty'        => round($totalQuantityProducing, 2),
            ],
        ];
    }

    protected function buildWeekColumns(Carbon $date): array
    {
        $start = (clone $date)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end = (clone $date)->endOfWeek(Carbon::SUNDAY)->endOfDay();
        $columns = [];

        for ($d = 0; $d < 7; $d++) {
            $day = (clone $start)->addDays($d);
            $columns[] = [
                'label'      => $day->format('D'),
                'sublabel'   => $day->format('d M'),
                'is_today'   => $day->isToday(),
                'is_weekend' => in_array($day->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
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
                'label'      => (string) $d,
                'sublabel'   => $day->format('D')[0],
                'is_today'   => $day->isToday(),
                'is_weekend' => in_array($day->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
            ];
        }

        return [$start, $end, $columns];
    }

    protected function getOrderColorTheme(string $state, bool $isOverdue): array
    {
        if ($isOverdue) {
            return [
                'bg'     => 'bg-rose-600 hover:bg-rose-500',
                'border' => 'border-rose-400 ring-2 ring-rose-500/50',
                'text'   => 'text-white',
                'badge'  => 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
            ];
        }

        return match ($state) {
            ManufacturingOrderState::PROGRESS->value => [
                'bg'     => 'bg-amber-600 hover:bg-amber-500',
                'border' => 'border-amber-400',
                'text'   => 'text-white',
                'badge'  => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
            ],
            ManufacturingOrderState::CONFIRMED->value => [
                'bg'     => 'bg-blue-600 hover:bg-blue-500',
                'border' => 'border-blue-400',
                'text'   => 'text-white',
                'badge'  => 'bg-blue-500/15 text-blue-400 border border-blue-500/30',
            ],
            ManufacturingOrderState::TO_CLOSE->value => [
                'bg'     => 'bg-indigo-600 hover:bg-indigo-500',
                'border' => 'border-indigo-400',
                'text'   => 'text-white',
                'badge'  => 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/30',
            ],
            ManufacturingOrderState::DONE->value => [
                'bg'     => 'bg-emerald-600 hover:bg-emerald-500',
                'border' => 'border-emerald-400',
                'text'   => 'text-white',
                'badge'  => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
            ],
            ManufacturingOrderState::CANCEL->value => [
                'bg'     => 'bg-gray-600 hover:bg-gray-500',
                'border' => 'border-gray-500',
                'text'   => 'text-white',
                'badge'  => 'bg-gray-500/15 text-gray-400 border border-gray-500/30',
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
