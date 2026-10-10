<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Pages;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Models\WorkCenterProductivityLog;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Security\Models\User;

class ShopFloor extends Page
{
    protected static ?string $cluster = Operations::class;

    protected static ?string $slug = 'shop-floor';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?int $navigationSort = 1;

    protected string $view = 'manufacturing::filament.clusters.operations.pages.shop-floor';

    public ?int $selectedWorkCenterId = null;

    public string $statusFilter = 'ready_and_running'; // 'ready_and_running', 'ready', 'progress', 'waiting', 'done', 'all'

    public bool $onlyMyWork = false;

    public string $search = '';

    public string $barcodeInput = '';

    public ?int $selectedWorkOrderId = null;

    public ?int $activeOperatorId = null;

    public bool $showOperatorModal = false;

    public array $recordQtys = [];

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return (bool) settings(OperationSettings::class)->enable_work_orders;
    }

    public static function getNavigationLabel(): string
    {
        return 'Shop Floor';
    }

    public function getTitle(): string
    {
        return 'Shop Floor';
    }

    public function getMaxContentWidth(): \Filament\Support\Enums\MaxWidth|string|null
    {
        return 'full';
    }

    public function mount(): void
    {
        $this->activeOperatorId = Auth::id();
    }

    public function selectWorkCenter(?int $id): void
    {
        $this->selectedWorkCenterId = $id;
    }

    public function setStatusFilter(string $status): void
    {
        if (in_array($status, ['ready_and_running', 'ready', 'progress', 'waiting', 'done', 'all'])) {
            $this->statusFilter = $status;
        }
    }

    public function toggleMyWork(): void
    {
        $this->onlyMyWork = ! $this->onlyMyWork;
    }

    public function saveRecordedQty(int $id): void
    {
        $qty = isset($this->recordQtys[$id]) ? (float) $this->recordQtys[$id] : null;
        if ($qty === null) {
            return;
        }

        $workOrder = WorkOrder::find($id);
        if (! $workOrder) {
            return;
        }

        $workOrder->update(['quantity_produced' => $qty]);
        if ($workOrder->manufacturingOrder) {
            $workOrder->manufacturingOrder->update(['quantity_producing' => $qty]);
        }

        Notification::make()
            ->title('Quantity Recorded')
            ->body("Recorded {$qty} for {$workOrder->name}.")
            ->success()
            ->send();
    }

    public function openDetailModal(int $id): void
    {
        $this->selectedWorkOrderId = $id;
    }

    public function closeDetailModal(): void
    {
        $this->selectedWorkOrderId = null;
    }

    public function toggleOperatorModal(): void
    {
        $this->showOperatorModal = ! $this->showOperatorModal;
    }

    public function switchOperator(int $userId): void
    {
        $this->activeOperatorId = $userId;
        $this->showOperatorModal = false;

        $user = User::find($userId);
        Notification::make()
            ->title('Operator Switched')
            ->body("Active terminal operator is now {$user?->name}.")
            ->success()
            ->send();
    }

    public function startWorkOrder(int $id): void
    {
        try {
            $workOrder = WorkOrder::with(['blockedByWorkOrders', 'workCenter'])->findOrFail($id);

            // Block check
            if ($workOrder->working_state === WorkCenterWorkingState::BLOCKED) {
                Notification::make()
                    ->title('Station Blocked')
                    ->body("Cannot start: Work center {$workOrder->workCenter?->name} is currently blocked.")
                    ->warning()
                    ->send();

                return;
            }

            // Dependency check
            $unresolved = $workOrder->blockedByWorkOrders->filter(
                fn ($b) => ! in_array($b->state instanceof WorkOrderState ? $b->state->value : (string) $b->state, ['done', 'cancel'])
            );

            if ($unresolved->isNotEmpty()) {
                Notification::make()
                    ->title('Operation Blocked')
                    ->body("Cannot start: Waiting on preceding operation {$unresolved->first()->name}.")
                    ->danger()
                    ->send();

                return;
            }

            $workOrder->start();

            Notification::make()
                ->title('Operation Started')
                ->body("Work order {$workOrder->name} is now in progress.")
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
                ->title('Operation Paused')
                ->body("Work order {$workOrder->name} has been paused.")
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

    public function finishWorkOrder(int $id, ?float $qty = null): void
    {
        try {
            $workOrder = WorkOrder::with(['manufacturingOrder', 'dependentWorkOrders'])->findOrFail($id);

            if ($qty !== null && $qty > 0) {
                $workOrder->update(['quantity_produced' => $qty]);
                if ($workOrder->manufacturingOrder) {
                    $workOrder->manufacturingOrder->update(['quantity_producing' => $qty]);
                }
            }

            $workOrder->finish();

            // Notify about auto-unblocked dependent work orders
            $unblockedCount = 0;
            foreach ($workOrder->dependentWorkOrders as $dep) {
                $dep->refresh();
                if (($dep->state instanceof WorkOrderState ? $dep->state->value : $dep->state) === WorkOrderState::READY->value) {
                    $unblockedCount++;
                }
            }

            $bodyMsg = "Work order {$workOrder->name} marked as finished.";
            if ($unblockedCount > 0) {
                $bodyMsg .= " {$unblockedCount} next operation(s) are now READY.";
            }

            Notification::make()
                ->title('Work Order Finished')
                ->body($bodyMsg)
                ->success()
                ->send();

            if ($this->selectedWorkOrderId === $id) {
                $this->selectedWorkOrderId = null;
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Action Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function incrementProducedQty(int $id): void
    {
        $workOrder = WorkOrder::find($id);
        if (! $workOrder) {
            return;
        }

        $current = (float) ($workOrder->quantity_produced ?: 0);
        $target = (float) ($workOrder->manufacturingOrder?->quantity ?: 1);

        if ($current < $target) {
            $newQty = $current + 1;
            $workOrder->update(['quantity_produced' => $newQty]);
            if ($workOrder->manufacturingOrder) {
                $workOrder->manufacturingOrder->update(['quantity_producing' => $newQty]);
            }
        }
    }

    public function decrementProducedQty(int $id): void
    {
        $workOrder = WorkOrder::find($id);
        if (! $workOrder) {
            return;
        }

        $current = (float) ($workOrder->quantity_produced ?: 0);
        if ($current > 0) {
            $newQty = max(0, $current - 1);
            $workOrder->update(['quantity_produced' => $newQty]);
            if ($workOrder->manufacturingOrder) {
                $workOrder->manufacturingOrder->update(['quantity_producing' => $newQty]);
            }
        }
    }

    public function quickFillQty(int $id): void
    {
        $workOrder = WorkOrder::find($id);
        if (! $workOrder) {
            return;
        }

        $target = (float) ($workOrder->manufacturingOrder?->quantity ?: 1);
        $workOrder->update(['quantity_produced' => $target]);
        if ($workOrder->manufacturingOrder) {
            $workOrder->manufacturingOrder->update(['quantity_producing' => $target]);
        }

        Notification::make()
            ->title('Quantity Updated')
            ->body("Produced quantity set to full target ({$target}).")
            ->success()
            ->send();
    }

    public function reassignWorkCenter(int $workOrderId, int $newWorkCenterId): void
    {
        try {
            $workOrder = WorkOrder::findOrFail($workOrderId);
            $newWc = WorkCenter::findOrFail($newWorkCenterId);

            $workOrder->update(['work_center_id' => $newWorkCenterId]);

            Notification::make()
                ->title('Work Center Reassigned')
                ->body("Operation moved to {$newWc->name}.")
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

    public function toggleWorkCenterBlocked(int $workCenterId): void
    {
        try {
            $wc = WorkCenter::findOrFail($workCenterId);
            $isBlocked = $wc->working_state === WorkCenterWorkingState::BLOCKED;

            $newState = $isBlocked ? WorkCenterWorkingState::NORMAL : WorkCenterWorkingState::BLOCKED;
            $wc->update(['working_state' => $newState]);

            $label = $isBlocked ? 'unblocked' : 'BLOCKED';
            Notification::make()
                ->title("Station {$wc->name} {$label}")
                ->body($isBlocked ? 'Work center is ready for production.' : 'Work center marked as blocked/down.')
                ->color($isBlocked ? 'success' : 'danger')
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Action Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function handleBarcodeInput(): void
    {
        $code = trim($this->barcodeInput);
        $this->barcodeInput = '';

        if (! $code) {
            return;
        }

        // Try to match Work Order
        $wo = WorkOrder::query()
            ->where('company_id', current_company_id())
            ->where(function ($q) use ($code) {
                $q->where('name', $code)
                    ->orWhere('id', $code)
                    ->orWhereHas('manufacturingOrder', fn ($m) => $m->where('name', $code));
            })
            ->first();

        if ($wo) {
            $this->selectedWorkOrderId = $wo->id;
            Notification::make()
                ->title('Barcode Matched')
                ->body("Work order {$wo->name} loaded.")
                ->success()
                ->send();

            return;
        }

        // Try to match User / Operator PIN
        $user = User::query()
            ->where('id', $code)
            ->orWhere('email', $code)
            ->orWhere('name', $code)
            ->first();

        if ($user) {
            $this->switchOperator($user->id);

            return;
        }

        Notification::make()
            ->title('Barcode Not Found')
            ->body("No work order or operator found for '{$code}'.")
            ->warning()
            ->send();
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
            'manufacturingOrder.moveRaw.product.uom',
            'operation',
            'blockedByWorkOrders',
            'dependentWorkOrders',
            'productivityLogs.assignedUser',
            'rawMaterialMoves.product.uom',
        ])->find($this->selectedWorkOrderId);
    }

    #[Computed]
    public function activeOperator(): ?User
    {
        return User::find($this->activeOperatorId ?: Auth::id());
    }

    #[Computed]
    public function availableOperators()
    {
        return User::query()
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'email']);
    }

    #[Computed]
    public function shopFloorData(): array
    {
        $companyId = current_company_id();

        // 1. Work Centers with live counts
        $allWorkCenters = WorkCenter::query()
            ->where('company_id', $companyId)
            ->orderBy('sort')
            ->get();

        // 2. Query work orders
        $query = WorkOrder::query()
            ->with([
                'workCenter',
                'manufacturingOrder.product.uom',
                'manufacturingOrder.assignedUser',
                'operation',
                'blockedByWorkOrders',
                'dependentWorkOrders',
                'productivityLogs.assignedUser',
                'rawMaterialMoves.product.uom',
            ])
            ->whereHas('manufacturingOrder', fn ($q) => $q->where('company_id', $companyId));

        if ($this->selectedWorkCenterId) {
            $query->where('work_center_id', $this->selectedWorkCenterId);
        }

        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhereHas('manufacturingOrder', function ($m) use ($s) {
                        $m->where('name', 'like', "%{$s}%")
                            ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$s}%"));
                    });
            });
        }

        $allOrders = $query->get();

        // Count metrics across current scope
        $stats = [
            'total'       => $allOrders->count(),
            'ready'       => 0,
            'progress'    => 0,
            'waiting'     => 0,
            'done'        => 0,
        ];

        $cards = [];

        foreach ($allOrders as $wo) {
            $stateVal = $wo->state instanceof WorkOrderState ? $wo->state->value : (string) $wo->state;

            // Classify status
            if ($stateVal === WorkOrderState::PROGRESS->value) {
                $stats['progress']++;
            } elseif ($stateVal === WorkOrderState::READY->value) {
                $stats['ready']++;
            } elseif ($stateVal === WorkOrderState::DONE->value) {
                $stats['done']++;
            } else {
                $stats['waiting']++;
            }

            // Apply status filter
            if ($this->statusFilter === 'ready_and_running') {
                if (! in_array($stateVal, [WorkOrderState::READY->value, WorkOrderState::PROGRESS->value])) {
                    continue;
                }
            } elseif ($this->statusFilter === 'ready' && $stateVal !== WorkOrderState::READY->value) {
                continue;
            } elseif ($this->statusFilter === 'progress' && $stateVal !== WorkOrderState::PROGRESS->value) {
                continue;
            } elseif ($this->statusFilter === 'waiting' && ! in_array($stateVal, [WorkOrderState::PENDING->value, WorkOrderState::WAITING->value])) {
                continue;
            } elseif ($this->statusFilter === 'done' && $stateVal !== WorkOrderState::DONE->value) {
                continue;
            }

            // My work filter
            if ($this->onlyMyWork) {
                $isMyWo = $wo->manufacturingOrder?->assigned_user_id === Auth::id()
                    || $wo->productivityLogs->contains(fn ($l) => $l->assigned_user_id === Auth::id());
                if (! $isMyWo) {
                    continue;
                }
            }

            // Dependencies
            $blockingWos = $wo->blockedByWorkOrders->map(fn ($b) => [
                'id'      => $b->id,
                'name'    => $b->name,
                'state'   => $b->state instanceof WorkOrderState ? $b->state->value : (string) $b->state,
                'is_done' => in_array($b->state instanceof WorkOrderState ? $b->state->value : (string) $b->state, ['done', 'cancel']),
            ])->all();

            $isBlocked = collect($blockingWos)->contains(fn ($b) => ! $b['is_done']);

            $targetQty = (float) ($wo->manufacturingOrder?->quantity ?: 1);
            $producedQty = (float) ($wo->quantity_produced ?: $wo->manufacturingOrder?->quantity_producing ?: 0);
            $progressPct = $targetQty > 0 ? min(100, round(($producedQty / $targetQty) * 100)) : 0;

            if (! isset($this->recordQtys[$wo->id])) {
                $this->recordQtys[$wo->id] = (int) $producedQty;
            }

            // Active timers & worker logs
            $activeLog = $wo->productivityLogs->first(fn ($log) => ! $log->finished_at);
            $activeWorkerName = $activeLog?->assignedUser?->name;

            // Elapsed time calculation
            $actualMinutes = (float) $wo->duration;
            if ($activeLog && $activeLog->started_at) {
                $actualMinutes += max(0, Carbon::parse($activeLog->started_at)->diffInMinutes(now()));
            }

            $cards[] = [
                'id'                      => $wo->id,
                'name'                    => $wo->name,
                'mo_id'                   => $wo->manufacturing_order_id,
                'mo_name'                 => $wo->manufacturingOrder?->name ?? '—',
                'source'                  => $wo->manufacturingOrder?->source ?? $wo->manufacturingOrder?->origin ?? $wo->manufacturingOrder?->name ?? '—',
                'work_center_id'          => $wo->work_center_id,
                'work_center_name'        => $wo->workCenter?->name ?? 'Unassigned',
                'work_center_code'        => $wo->workCenter?->code,
                'is_station_blocked'      => $wo->workCenter?->working_state === WorkCenterWorkingState::BLOCKED,
                'product_name'            => $wo->manufacturingOrder?->product?->name ?? '—',
                'uom'                     => $wo->manufacturingOrder?->product?->uom?->name ?? 'Units',
                'quantity_target'         => $targetQty,
                'quantity_produced'       => $producedQty,
                'progress_percent'        => $progressPct,
                'state'                   => $stateVal,
                'state_label'             => $wo->state instanceof WorkOrderState ? $wo->state->getLabel() : ucfirst($stateVal),
                'expected_duration'       => (float) $wo->expected_duration,
                'actual_duration'         => round($actualMinutes),
                'started_at_formatted'    => $wo->started_at ? Carbon::parse($wo->started_at)->format('d M, H:i') : null,
                'is_blocked'              => $isBlocked,
                'blocking_wos'            => $blockingWos,
                'active_worker_name'      => $activeWorkerName,
                'has_active_timer'        => (bool) $activeLog,
                'is_ready'                => $stateVal === WorkOrderState::READY->value && ! $isBlocked,
                'is_progress'             => $stateVal === WorkOrderState::PROGRESS->value,
                'is_done'                 => $stateVal === WorkOrderState::DONE->value,
            ];
        }

        // Sort cards: In Progress first, then Ready, then Waiting, then Done
        usort($cards, function ($a, $b) {
            $priority = ['progress' => 0, 'ready' => 1, 'waiting' => 2, 'pending' => 2, 'done' => 3, 'cancel' => 4];
            $pA = $priority[$a['state']] ?? 5;
            $pB = $priority[$b['state']] ?? 5;

            return $pA <=> $pB;
        });

        // Compute badge counts for work centers
        $wcList = $allWorkCenters->map(function ($wc) use ($allOrders) {
            $countReady = $allOrders->where('work_center_id', $wc->id)->where('state', WorkOrderState::READY->value)->count();
            $countProgress = $allOrders->where('work_center_id', $wc->id)->where('state', WorkOrderState::PROGRESS->value)->count();

            return [
                'id'             => $wc->id,
                'name'           => $wc->name,
                'code'           => $wc->code,
                'is_blocked'     => $wc->working_state === WorkCenterWorkingState::BLOCKED,
                'ready_count'    => $countReady,
                'progress_count' => $countProgress,
                'total_active'   => $countReady + $countProgress,
            ];
        })->all();

        return [
            'work_centers' => $wcList,
            'stats'        => $stats,
            'cards'        => $cards,
        ];
    }
}
