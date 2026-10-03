<?php

namespace Webkul\SaleProductPack\Services;

use Webkul\Product\Models\Product;
use Webkul\Sale\Models\OrderLine;

class SalePackManager
{
    protected static bool $isExpanding = false;

    public static function handleOrderLineSaved(OrderLine $orderLine): void
    {
        if (static::$isExpanding) {
            return;
        }

        // If this is a child pack component, prevent infinite loop
        if ($orderLine->is_pack_component || $orderLine->pack_parent_line_id) {
            return;
        }

        $product = Product::with('packLines.product')->find($orderLine->product_id);

        if (! $product || ! $product->is_pack) {
            return;
        }

        static::$isExpanding = true;

        try {
            $packType = $product->pack_type ?? 'detailed';
            $packComponentPrice = $product->pack_component_price ?? 'detailed';
            $packModifiable = (bool) ($product->pack_modifiable ?? false);

            $orderLine->is_pack = true;
            $orderLine->pack_type = $packType;
            $orderLine->pack_component_price = $packComponentPrice;
            $orderLine->pack_modifiable = $packModifiable;

            $packLines = $product->packLines;

            if ($packType === 'detailed' && $packLines->isNotEmpty()) {
                $componentsSumPrice = 0;

                foreach ($packLines as $packLine) {
                    $componentProduct = $packLine->product;
                    if (! $componentProduct) {
                        continue;
                    }

                    $componentUnitPrice = (float) ($componentProduct->price ?? 0);
                    $componentsSumPrice += $componentUnitPrice * (float) $packLine->quantity;

                    $componentQty = (float) $packLine->quantity * (float) $orderLine->product_qty;

                    // Pricing policy per component
                    $finalComponentPrice = match ($packComponentPrice) {
                        'detailed'  => $componentUnitPrice,
                        'totalized' => 0.0,
                        'ignored'   => 0.0,
                        default     => $componentUnitPrice,
                    };

                    $childLine = OrderLine::where('pack_parent_line_id', $orderLine->id)
                        ->where('product_id', $packLine->product_id)
                        ->first();

                    if ($childLine) {
                        $childLine->update([
                            'product_qty'     => $componentQty,
                            'product_uom_qty' => $componentQty,
                            'price_unit'      => $finalComponentPrice,
                            'price_subtotal'  => $componentQty * $finalComponentPrice,
                            'price_total'     => $componentQty * $finalComponentPrice,
                        ]);
                    } else {
                        OrderLine::create([
                            'order_id'            => $orderLine->order_id,
                            'company_id'          => $orderLine->company_id,
                            'currency_id'         => $orderLine->currency_id,
                            'order_partner_id'    => $orderLine->order_partner_id,
                            'salesman_id'         => $orderLine->salesman_id,
                            'product_id'          => $packLine->product_id,
                            'product_uom_id'      => $componentProduct->uom_id ?? $orderLine->product_uom_id,
                            'pack_parent_line_id' => $orderLine->id,
                            'is_pack_component'   => true,
                            'pack_depth'          => 1,
                            'name'                => $componentProduct->name.' ('.__('Komponen Paket').')',
                            'product_qty'         => $componentQty,
                            'product_uom_qty'     => $componentQty,
                            'price_unit'          => $finalComponentPrice,
                            'discount'            => 0,
                            'price_subtotal'      => $componentQty * $finalComponentPrice,
                            'price_total'         => $componentQty * $finalComponentPrice,
                            'state'               => $orderLine->state,
                        ]);
                    }
                }

                // If totalized in main product, main product price is pack price + components sum
                if ($packComponentPrice === 'totalized') {
                    $totalPrice = (float) ($product->price ?? 0) + $componentsSumPrice;
                    if (abs((float) $orderLine->price_unit - $totalPrice) > 0.001) {
                        $orderLine->price_unit = $totalPrice;
                        $orderLine->price_subtotal = $orderLine->product_qty * $totalPrice;
                        $orderLine->price_total = $orderLine->product_qty * $totalPrice;
                    }
                }
            } elseif ($packType === 'non_detailed') {
                // Remove any leftover child lines if previously detailed
                OrderLine::where('pack_parent_line_id', $orderLine->id)->delete();

                // If non_detailed, price is pack price + sum of components
                $componentsSumPrice = 0;
                foreach ($packLines as $packLine) {
                    $componentsSumPrice += (float) ($packLine->product?->price ?? 0) * (float) $packLine->quantity;
                }

                $totalPrice = (float) ($product->price ?? 0) + $componentsSumPrice;
                if ($totalPrice > 0 && (float) $orderLine->price_unit < $totalPrice) {
                    $orderLine->price_unit = $totalPrice;
                    $orderLine->price_subtotal = $orderLine->product_qty * $totalPrice;
                    $orderLine->price_total = $orderLine->product_qty * $totalPrice;
                }
            }

            $orderLine->saveQuietly();
        } finally {
            static::$isExpanding = false;
        }
    }

    public static function handleOrderLineDeleted(OrderLine $orderLine): void
    {
        // Delete all child component lines when parent pack line is deleted
        OrderLine::where('pack_parent_line_id', $orderLine->id)->delete();
    }
}
