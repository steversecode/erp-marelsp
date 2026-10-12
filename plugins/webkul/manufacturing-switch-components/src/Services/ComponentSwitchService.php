<?php

namespace Webkul\ManufacturingSwitchComponents\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Product;
use Webkul\ManufacturingSwitchComponents\Models\ComponentSwitch;
use Webkul\Security\Models\User;

class ComponentSwitchService
{
    /**
     * Switch an existing component move on a Manufacturing Order with a replacement product.
     *
     * @throws Exception
     */
    public function switchComponent(
        Order $order,
        Move $originalMove,
        Product $replacementProduct,
        float $replacementQuantity,
        ?string $reason = null,
        ?User $user = null
    ): ComponentSwitch {
        if (in_array($order->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])) {
            throw new Exception(__('manufacturing_switch_components::app.errors.order_already_finished'));
        }

        if ($originalMove->raw_material_order_id !== $order->id) {
            throw new Exception(__('manufacturing_switch_components::app.errors.invalid_move'));
        }

        if (in_array($originalMove->state, [MoveState::DONE, MoveState::CANCELED])) {
            throw new Exception(__('manufacturing_switch_components::app.errors.move_already_closed'));
        }

        if ($replacementQuantity <= 0) {
            throw new Exception(__('manufacturing_switch_components::app.errors.invalid_quantity'));
        }

        return DB::transaction(function () use (
            $order,
            $originalMove,
            $replacementProduct,
            $replacementQuantity,
            $reason,
            $user
        ): ComponentSwitch {
            $consumedQty = (float) ($originalMove->quantity ?? 0);

            // 1. Process original move: if partially consumed, adjust product_uom_qty. If not consumed, cancel move.
            if ($consumedQty > 0) {
                $originalMove->update([
                    'product_uom_qty' => $consumedQty,
                ]);
            } else {
                InventoryFacade::cancelMoves(collect([$originalMove]));
            }

            // 2. Create the replacement stock move
            $newMove = Move::create([
                'name'                    => $order->name,
                'raw_material_order_id'   => $order->id,
                'product_id'              => $replacementProduct->id,
                'product_uom_qty'         => $replacementQuantity,
                'uom_id'                  => $replacementProduct->uom_id,
                'source_location_id'      => $originalMove->source_location_id ?: $order->source_location_id,
                'destination_location_id' => $order->production_location_id,
                'operation_type_id'       => $order->operation_type_id,
                'bom_line_id'             => $originalMove->bom_line_id,
                'company_id'              => $order->company_id,
                'state'                   => MoveState::DRAFT,
            ]);

            // 3. Confirm and procure new move if MO is not in DRAFT
            if ($order->state !== ManufacturingOrderState::DRAFT) {
                $newMove->resolveProcureMethod();
                $newMove->save();

                InventoryFacade::confirmMoves(collect([$newMove]));
            }

            // 4. Record history for audit trail
            $history = ComponentSwitch::create([
                'order_id'               => $order->id,
                'original_move_id'       => $originalMove->id,
                'original_product_id'    => $originalMove->product_id,
                'original_quantity'      => $originalMove->product_uom_qty,
                'original_uom_id'        => $originalMove->uom_id,
                'replacement_move_id'    => $newMove->id,
                'replacement_product_id' => $replacementProduct->id,
                'replacement_quantity'   => $replacementQuantity,
                'replacement_uom_id'     => $replacementProduct->uom_id,
                'reason'                 => $reason,
                'switched_by_id'         => $user?->id ?? auth()->id(),
            ]);

            // 5. Add log note to MO chatter if available
            if (method_exists($order, 'addMessage')) {
                $originalProductName = $originalMove->product?->name ?? 'Komponen Asal';
                $originalUomName = $originalMove->uom?->name ?? '';
                $newUomName = $replacementProduct->uom?->name ?? '';
                $userName = $user?->name ?? auth()->user()?->name ?? 'System';

                $body = sprintf(
                    "<p><strong>[Switch Component]</strong> Komponen <strong>%s</strong> (%.4f %s) diganti dengan <strong>%s</strong> (%.4f %s).</p><p><strong>Alasan:</strong> %s</p><p><strong>Oleh:</strong> %s</p>",
                    e($originalProductName),
                    (float) $originalMove->product_uom_qty,
                    e($originalUomName),
                    e($replacementProduct->name),
                    $replacementQuantity,
                    e($newUomName),
                    e($reason ?: '—'),
                    e($userName)
                );

                $order->addMessage([
                    'body' => $body,
                    'type' => 'comment',
                ]);
            }

            return $history;
        });
    }
}
