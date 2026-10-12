<?php

namespace Webkul\ManufacturingSwitchComponents\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Throwable;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Product;
use Webkul\ManufacturingSwitchComponents\Services\ComponentSwitchService;
use Webkul\Product\Enums\ProductType;

class SwitchComponentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'manufacturing.order.switch-component';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('manufacturing_switch_components::app.action.label'))
            ->icon('heroicon-m-arrows-right-left')
            ->color('warning')
            ->visible(function (?Order $record): bool {
                $record ??= $this->getRecord();

                if (! $record) {
                    return false;
                }

                if (in_array($record->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])) {
                    return false;
                }

                return $record->rawMaterialMoves()
                    ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
                    ->exists();
            })
            ->modalHeading(__('manufacturing_switch_components::app.modal.heading'))
            ->modalDescription(__('manufacturing_switch_components::app.modal.description'))
            ->modalSubmitActionLabel(__('manufacturing_switch_components::app.modal.submit'))
            ->form(function (): array {
                /** @var Order $order */
                $order = $this->getRecord();

                $activeMoves = $order
                    ? $order->rawMaterialMoves()
                        ->with(['product', 'uom'])
                        ->whereNotIn('state', [MoveState::DONE, MoveState::CANCELED])
                        ->get()
                    : collect();

                return [
                    Select::make('original_move_id')
                        ->label(__('manufacturing_switch_components::app.modal.fields.original_component'))
                        ->options(function () use ($activeMoves): array {
                            return $activeMoves->mapWithKeys(function (Move $move): array {
                                $remaining = max(0, (float) $move->product_uom_qty - (float) ($move->quantity ?? 0));
                                $label = sprintf(
                                    '%s (Kebutuhan: %.4f %s | Sisa: %.4f)',
                                    $move->product?->name ?? 'Unknown',
                                    (float) $move->product_uom_qty,
                                    $move->uom?->name ?? '',
                                    $remaining
                                );

                                return [$move->id => $label];
                            })->all();
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state) use ($activeMoves): void {
                            $selectedMove = $activeMoves->firstWhere('id', (int) $state);

                            if ($selectedMove) {
                                $remaining = max(0, (float) $selectedMove->product_uom_qty - (float) ($selectedMove->quantity ?? 0));
                                $set('replacement_quantity', $remaining > 0 ? $remaining : (float) $selectedMove->product_uom_qty);
                                $set('original_uom_name', $selectedMove->uom?->name ?? '');
                            }
                        }),

                    Select::make('replacement_product_id')
                        ->label(__('manufacturing_switch_components::app.modal.fields.replacement_product'))
                        ->options(function (Get $get) use ($order, $activeMoves): array {
                            $companyId = $order?->company_id ?? current_company_id();

                            $query = Product::query()
                                ->where('type', ProductType::GOODS)
                                ->where(owned_by_company($companyId));

                            if ($moveId = $get('original_move_id')) {
                                $selectedMove = $activeMoves->firstWhere('id', (int) $moveId);
                                if ($selectedMove) {
                                    $query->where('id', '!=', $selectedMove->product_id);
                                }
                            }

                            return $query->limit(50)->pluck('name', 'id')->all();
                        })
                        ->getSearchResultsUsing(function (string $search, Get $get) use ($order, $activeMoves): array {
                            $companyId = $order?->company_id ?? current_company_id();

                            $query = Product::query()
                                ->where('type', ProductType::GOODS)
                                ->where(owned_by_company($companyId))
                                ->where('name', 'like', "%{$search}%");

                            if ($moveId = $get('original_move_id')) {
                                $selectedMove = $activeMoves->firstWhere('id', (int) $moveId);
                                if ($selectedMove) {
                                    $query->where('id', '!=', $selectedMove->product_id);
                                }
                            }

                            return $query->limit(50)->pluck('name', 'id')->all();
                        })
                        ->getOptionLabelUsing(fn ($value): ?string => Product::find($value)?->name)
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            $product = Product::find($state);
                            $set('replacement_uom_name', $product?->uom?->name ?? '');
                        }),

                    TextInput::make('replacement_quantity')
                        ->label(__('manufacturing_switch_components::app.modal.fields.replacement_quantity'))
                        ->numeric()
                        ->minValue(0.0001)
                        ->required()
                        ->suffix(fn (Get $get): string => (string) ($get('replacement_uom_name') ?: '—')),

                    Textarea::make('reason')
                        ->label(__('manufacturing_switch_components::app.modal.fields.reason'))
                        ->placeholder(__('manufacturing_switch_components::app.modal.placeholders.reason'))
                        ->rows(3)
                        ->required(),
                ];
            })
            ->action(function (Order $record, array $data, ?Component $livewire = null): void {
                try {
                    $originalMove = Move::findOrFail($data['original_move_id']);
                    $replacementProduct = Product::findOrFail($data['replacement_product_id']);

                    $service = app(ComponentSwitchService::class);

                    $service->switchComponent(
                        order: $record,
                        originalMove: $originalMove,
                        replacementProduct: $replacementProduct,
                        replacementQuantity: (float) $data['replacement_quantity'],
                        reason: $data['reason'],
                        user: auth()->user()
                    );

                    $record->refresh();

                    if ($livewire && method_exists($livewire, 'updateForm')) {
                        $livewire->updateForm();
                    }

                    Notification::make()
                        ->title(__('manufacturing_switch_components::app.notifications.switched_title'))
                        ->body(sprintf(
                            __('manufacturing_switch_components::app.notifications.switched_body'),
                            $originalMove->product?->name,
                            $replacementProduct->name
                        ))
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->title(__('manufacturing_switch_components::app.notifications.failed_title'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();

                    $this->halt();
                }
            });
    }
}
