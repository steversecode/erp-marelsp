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

    public function toggleWorkCenterBlocked(int $id): void
    {
        try {
            $wc = WorkCenter::findOrFail($id);

            $newState = $wc->working_state === WorkCenterWorkingState::BLOCKED
                ? WorkCenterWorkingState::NORMAL
                : WorkCenterWorkingState::BLOCKED;

            $wc->update(['working_state' => $newState]);

            $label = $newState === WorkCenterWorkingState::BLOCKED ? 'Blocked' : 'Unblocked (Normal)';

            Notification::make()
                ->title("Work Center {$label}")
                ->body("{$wc->name} status has been updated to {$label}.")
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

    public function reassignWorkCenter(int $workOrderId, int $newWorkCenterId): void
    {
        try {
            $workOrder = WorkOrder::with(['workCenter', 'product'])->findOrFail($workOrderId);
            $newWorkCenter = WorkCenter::findOrFail($newWorkCenterId);

            $oldName = $workOrder->workCenter?->name ?? 'None';

            $workOrder->work_center_id = $newWorkCenter->id;

            // Recalculate expected duration for new work center
            $expectedDuration = $workOrder->getExpectedDuration(alternativeWorkCenter: $newWorkCenter);
            $workOrder->expected_duration = $expectedDuration;

            if ($workOrder->started_at) {
                $workOrder->finished_at = $workOrder->calculateDateFinished($workOrder->started_at);
            }

            $workOrder->save();

            Notification::make()
                ->title('Work Center Reassigned')
                ->body("Work order {$workOrder->name} moved from {$oldName} to {$newWorkCenter->name}. Expected duration recalculated to {$expectedDuration}m.")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Reassignment Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function startWorkOrder(int $id): void
    {
        try {
            $workOrder = WorkOrder::with('workCenter', 'blockedByWorkOrders')->findOrFail($id);

            if ($workOrder->workCenter?->working_state === WorkCenterWorkingState::BLOCKED) {
                throw new \Exception("Cannot start: Work center '{$workOrder->workCenter->name}' is currently BLOCKED. Please unblock it first.");
            }

            $undoneBlockers = $workOrder->blockedByWorkOrders->filter(
                fn ($b) => ! in_array($b->state instanceof WorkOrderState ? $b->state->value : (string) $b->state, ['done', 'cancel'])
            );

            if ($undoneBlockers->isNotEmpty()) {
                $names = $undoneBlockers->pluck('name')->implode(', ');
                throw new \Exception("Cannot start: Preceding operation(s) [{$names}] are not yet finished.");
            }

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

    public function pauseWorkOrder(int $id): void
    {
        try {
            $workOrder = WorkOrder::findOrFail($id);
            $workOrder->pending();

            Notification::make()
                ->title('Work Order Paused')
                ->body("Work order {$workOrder->name} paused and timer stopped.")
                ->info()
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
            'workCenter.alternativeWorkCenters',
            'manufacturingOrder.product.uom',
            'manufacturingOrder.assignedUser',
            'operation',
            'product.uom',
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
            'day'   => $this->buildDayColumns($current),
            'week'  => $this->buildWeekColumns($current),
            'month' => $this->buildMonthColumns($current),
            default => $this->buildWeekColumns($current),
        };

        $rangeStartTs = $rangeStart->timestamp;
        $rangeEndTs = $rangeEnd->timestamp;
        $totalSeconds = max(1, $rangeEndTs - $rangeStartTs);

        // Compute available working hours for the period (Odoo progress bar capacity model)
        $availablePeriodHours = match ($this->viewMode) {
            'day'   => 8.0,
            'week'  => 40.0,
            'month' => $this->calculateWorkingHoursInMonth($current),
            default => 40.0,
        };

        // Work centers
        $workCentersQuery = WorkCenter::query()
            ->where('company_id', current_company_id())
            ->with(['alternativeWorkCenters', 'calendar']);

        if ($this->workCenterFilter) {
            $workCentersQuery->where('id', $this->workCenterFilter);
        }

        $workCenters = $workCentersQuery->orderBy('sort')->get();

        // Work orders with dependencies
        $workOrdersQuery = WorkOrder::query()
            ->with([
                'workCenter',
                'manufacturingOrder.product.uom',
                'operation',
                'blockedByWorkOrders',
                'dependentWorkOrders',
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

                // Check overlap with timeline window
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

                // Coordinates
                $clampedStartTs = max($rangeStartTs, $startTs);
                $clampedEndTs = min($rangeEndTs, $endTs);

                $leftPercent = (($clampedStartTs - $rangeStartTs) / $totalSeconds) * 100;
                $widthPercent = (($clampedEndTs - $clampedStartTs) / $totalSeconds) * 100;

                $leftPercent = max(0, min(97.0, $leftPercent));
                $widthPercent = max(3.0, min(100 - $leftPercent, $widthPercent));

                // Dependencies status
                $blockingWos = $wo->blockedByWorkOrders->map(fn ($b) => [
                    'id'      => $b->id,
                    'name'    => $b->name,
                    'state'   => $b->state instanceof WorkOrderState ? $b->state->value : (string) $b->state,
                    'is_done' => in_array($b->state instanceof WorkOrderState ? $b->state->value : (string) $b->state, ['done', 'cancel']),
                ])->all();

                $isBlocked = collect($blockingWos)->contains(fn ($b) => ! $b['is_done']);

                $items[] = [
                    'id'              => $wo->id,
                    'name'            => $wo->name,
                    'mo_id'           => $wo->manufacturing_order_id,
                    'mo_name'         => $wo->manufacturingOrder?->name ?? '—',
                    'product_name'    => $wo->manufacturingOrder?->product?->name ?? '—',
                    'quantity'        => (float) ($wo->quantity_produced ?: $wo->manufacturingOrder?->quantity ?: 0),
                    'uom'             => $wo->manufacturingOrder?->product?->uom?->name ?? '',
                    'state'           => $stateVal,
                    'state_label'     => $wo->state instanceof WorkOrderState ? $wo->state->getLabel() : ucfirst($stateVal),
                    'color_theme'     => $this->getStatusColorTheme($stateVal),
                    'start_formatted' => $woStart->format('d M H:i'),
                    'end_formatted'   => $woEnd->format('d M H:i'),
                    'duration_hours'  => round($expectedDuration / 60, 1),
                    'actual_hours'    => round((float) $wo->duration / 60, 1),
                    'left_percent'    => round($leftPercent, 2),
                    'width_percent'   => round($widthPercent, 2),
                    'is_in_progress'  => $stateVal === WorkOrderState::PROGRESS->value,
                    'is_ready'        => $stateVal === WorkOrderState::READY->value,
                    'is_done'         => $stateVal === WorkOrderState::DONE->value,
                    'blocking_wos'    => $blockingWos,
                    'is_blocked'      => $isBlocked,
                ];
            }

            $plannedHours = round($wcPlannedMinutes / 60, 1);
            $utilizationPct = round(($plannedHours / max(1, $availablePeriodHours)) * 100);

            $rows[] = [
                'work_center'         => $wc,
                'items'               => $items,
                'planned_hours'       => $plannedHours,
                'available_hours'     => $availablePeriodHours,
                'utilization_percent' => $utilizationPct,
                'is_overloaded'       => $utilizationPct > 100,
                'active_orders'       => count($items),
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
            'all_work_centers'    => $workCenters->map(fn ($wc) => ['id' => $wc->id, 'name' => $wc->name, 'code' => $wc->code])->all(),
            'stats'               => [
                'total_orders'  => $totalOrdersCount,
                'in_progress'   => $inProgressCount,
                'ready'         => $readyCount,
                'done'          => $doneCount,
                'planned_hours' => round($totalPlannedHours, 1),
                'work_centers'  => count($workCenters),
            ],
        ];
    }

    protected function calculateWorkingHoursInMonth(Carbon $date): float
    {
        $daysInMonth = $date->daysInMonth;
        $workingDays = 0;

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $day = (clone $date)->day($d);
            if (! in_array($day->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $workingDays++;
            }
        }

        return (float) ($workingDays * 8);
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
