<?php

namespace Webkul\MrpReport\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Security\Models\User;

class MrpReportService
{
    /**
     * Categories considered as Yarn / Benang.
     */
    public const BENANG_CATEGORIES = [
        'benang kk',
        'benang baju',
        'kain baju',
        'benang',
        'kain',
    ];

    /**
     * Categories considered as Accessories.
     */
    public const ACCESSORIES_CATEGORIES = [
        'aksesoris kk',
        'aksesoris vendor',
        'vendor aksesoris',
        'aksesoris baju',
        'accessories kk',
        'accessories vendor',
        'vendor accessories',
        'accessories baju',
        'aksesoris',
        'accessories',
    ];

    /**
     * Cache for resolved sales orders by MO or origin string.
     */
    protected array $salesOrderCache = [];

    /**
     * Recursively resolve the related Sales Order for a Manufacturing Order.
     */
    public function resolveSalesOrder(Order $order, array &$visited = [], int $depth = 0): ?object
    {
        if ($depth > 10 || ! class_exists('\Webkul\Sale\Models\Order')) {
            return null;
        }

        if (isset($this->salesOrderCache[$order->id])) {
            return $this->salesOrderCache[$order->id];
        }

        if (in_array($order->id, $visited, true)) {
            return null;
        }
        $visited[] = $order->id;

        $origin = trim((string) $order->origin);

        // 1. Direct origin matches Sales Order name (e.g. SO001)
        if (! empty($origin)) {
            $originParts = array_map('trim', explode(',', $origin));

            foreach ($originParts as $part) {
                if (str_starts_with(strtoupper($part), 'SO') || str_starts_with(strtoupper($part), 'S/')) {
                    $so = \Webkul\Sale\Models\Order::query()->where('name', $part)->first();
                    if ($so) {
                        return $this->salesOrderCache[$order->id] = $so;
                    }
                }
            }

            // 2. Origin references a parent MO (e.g. WH/MO/0001 or MO/0001)
            foreach ($originParts as $part) {
                if (str_contains(strtoupper($part), 'MO')) {
                    $parentMo = Order::query()->where('name', $part)->first();
                    if ($parentMo && ! in_array($parentMo->id, $visited, true)) {
                        $so = $this->resolveSalesOrder($parentMo, $visited, $depth + 1);
                        if ($so) {
                            return $this->salesOrderCache[$order->id] = $so;
                        }
                    }
                }
            }
        }

        // 3. Fallback: Parse child MO naming pattern like WH/MO/0001-001
        $name = (string) $order->name;
        if (str_contains($name, '-')) {
            $parts = explode('-', $name);
            $parentName = $parts[0] ?? '';
            if (! empty($parentName) && str_contains(strtoupper($parentName), 'MO')) {
                $parentMo = Order::query()->where('name', $parentName)->first();
                if ($parentMo && ! in_array($parentMo->id, $visited, true)) {
                    $so = $this->resolveSalesOrder($parentMo, $visited, $depth + 1);
                    if ($so) {
                        return $this->salesOrderCache[$order->id] = $so;
                    }
                }
            }
        }

        return $this->salesOrderCache[$order->id] = null;
    }

    /**
     * Get Delivery Date from related Sales Order (or deadline_at from MO).
     */
    public function getDeliveryDate(Order $order): ?string
    {
        $so = $this->resolveSalesOrder($order);

        if ($so) {
            $date = $so->commitment_date ?? $so->date_order ?? null;
            if ($date) {
                return is_string($date) ? date('d/m/Y', strtotime($date)) : $date->format('d/m/Y');
            }
        }

        return $order->deadline_at ? $order->deadline_at->format('d/m/Y') : null;
    }

    /**
     * Get Customer Name from related Sales Order or fallback company.
     */
    public function getCustomerName(Order $order): ?string
    {
        $so = $this->resolveSalesOrder($order);

        if ($so && ! empty($so->partner)) {
            return $so->partner->name;
        }

        return $order->company?->name;
    }

    /**
     * Get Source Name: prefers Stock Request Order / SO name, falls back to origin or MO name.
     */
    public function getSourceName(Order $order): string
    {
        $so = $this->resolveSalesOrder($order);

        if ($so) {
            return $so->name;
        }

        return $order->origin ?: $order->name;
    }

    /**
     * Get Product Size / Size Steam.
     */
    public function getProductSize(mixed $product): ?string
    {
        if (! $product) {
            return null;
        }

        if (isset($product->size) && ! empty($product->size)) {
            return (string) $product->size;
        }

        if (isset($product->size_steam) && ! empty($product->size_steam)) {
            return (string) $product->size_steam;
        }

        if (! empty($product->custom_fields) && is_array($product->custom_fields)) {
            foreach (['size', 'size_steam', 'ukuran'] as $key) {
                if (! empty($product->custom_fields[$key])) {
                    return (string) $product->custom_fields[$key];
                }
            }
        }

        return null;
    }

    /**
     * Check if a category belongs to Benang / Yarn.
     */
    public function isBenangCategory(?string $categoryName): bool
    {
        if (empty($categoryName)) {
            return false;
        }

        $normalized = strtolower(trim($categoryName));

        foreach (self::BENANG_CATEGORIES as $target) {
            if (str_contains($normalized, $target) || $normalized === $target) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a category belongs to Accessories.
     */
    public function isAccessoryCategory(?string $categoryName): bool
    {
        if (empty($categoryName)) {
            return false;
        }

        $normalized = strtolower(trim($categoryName));

        foreach (self::ACCESSORIES_CATEGORIES as $target) {
            if (str_contains($normalized, $target) || $normalized === $target) {
                return true;
            }
        }

        return false;
    }

    /**
     * Aggregate Benang / Yarn components across given Manufacturing Orders.
     */
    public function getBenangComponents(iterable $orders): array
    {
        $components = [];

        foreach ($orders as $order) {
            $order->loadMissing([
                'rawMaterialMoves.product.category',
                'rawMaterialMoves.uom',
            ]);

            foreach ($order->rawMaterialMoves as $move) {
                if ($move->state === MoveState::CANCEL) {
                    continue;
                }

                $product = $move->product;
                if (! $product) {
                    continue;
                }

                $categoryName = $product->category?->name ?? '';
                if (! $this->isBenangCategory($categoryName)) {
                    continue;
                }

                $key = $product->id.'_'.($move->uom_id ?? 0);

                if (! isset($components[$key])) {
                    $components[$key] = [
                        'product'       => $product,
                        'name'          => $product->name,
                        'reference'     => $product->reference,
                        'category'      => $categoryName ?: '-',
                        'uom'           => $move->uom?->name ?? 'Unit',
                        'qty'           => 0.0,
                    ];
                }

                $qty = (float) ($move->quantity ?? $move->product_uom_qty ?? 0);
                $components[$key]['qty'] += $qty;
            }
        }

        uasort($components, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return array_values($components);
    }

    /**
     * Aggregate Accessories components across given Manufacturing Orders.
     */
    public function getAccessoriesComponents(iterable $orders): array
    {
        $components = [];

        foreach ($orders as $order) {
            $order->loadMissing([
                'rawMaterialMoves.product.category',
                'rawMaterialMoves.uom',
            ]);

            foreach ($order->rawMaterialMoves as $move) {
                if ($move->state === MoveState::CANCEL) {
                    continue;
                }

                $product = $move->product;
                if (! $product) {
                    continue;
                }

                $categoryName = $product->category?->name ?? '';
                if (! $this->isAccessoryCategory($categoryName)) {
                    continue;
                }

                $key = $product->id.'_'.($move->uom_id ?? 0);

                if (! isset($components[$key])) {
                    $components[$key] = [
                        'product'   => $product,
                        'name'      => $product->name,
                        'reference' => $product->reference,
                        'category'  => $categoryName ?: '-',
                        'uom'       => $move->uom?->name ?? 'Unit',
                        'qty'       => 0.0,
                    ];
                }

                $qty = (float) ($move->quantity ?? $move->product_uom_qty ?? 0);
                $components[$key]['qty'] += $qty;
            }
        }

        uasort($components, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return array_values($components);
    }

    /**
     * Group orders and accessories components by Sales Order for "Bon Aksesoris".
     */
    public function getBonAksesorisGroupedData(iterable $orders): array
    {
        $groups = [];

        foreach ($orders as $order) {
            $so = $this->resolveSalesOrder($order);
            $groupKey = $so ? 'SO_'.$so->id : 'NOSO_'.$order->id;

            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'sales_order'    => $so,
                    'so_name'        => $so ? $so->name : __('mrp_report::app.labels.without_so'),
                    'customer_name'  => $so?->partner?->name ?? 'N/A',
                    'orders'         => [],
                    'product_names'  => [],
                    'total_quantity' => 0.0,
                    'components'     => [],
                ];
            }

            $groups[$groupKey]['orders'][] = $order;
            if ($order->product) {
                $groups[$groupKey]['product_names'][] = $order->product->name;
            }
            $groups[$groupKey]['total_quantity'] += (float) $order->quantity;

            // Accessories components for this MO
            $order->loadMissing([
                'rawMaterialMoves.product.category',
                'rawMaterialMoves.uom',
            ]);

            foreach ($order->rawMaterialMoves as $move) {
                if ($move->state === MoveState::CANCEL) {
                    continue;
                }

                $product = $move->product;
                if (! $product) {
                    continue;
                }

                $categoryName = $product->category?->name ?? '';
                if (! $this->isAccessoryCategory($categoryName)) {
                    continue;
                }

                $compKey = $product->id.'_'.($move->uom_id ?? 0);

                if (! isset($groups[$groupKey]['components'][$compKey])) {
                    $groups[$groupKey]['components'][$compKey] = [
                        'product'   => $product,
                        'name'      => $product->name,
                        'reference' => $product->reference,
                        'category'  => $categoryName ?: '-',
                        'uom'       => $move->uom?->name ?? 'Unit',
                        'qty'       => 0.0,
                    ];
                }

                $qty = (float) ($move->quantity ?? $move->product_uom_qty ?? 0);
                $groups[$groupKey]['components'][$compKey]['qty'] += $qty;
            }
        }

        foreach ($groups as &$group) {
            $group['product_names'] = array_values(array_unique($group['product_names']));
            uasort($group['components'], fn ($a, $b) => strcasecmp($a['name'], $b['name']));
            $group['components'] = array_values($group['components']);
        }

        return array_values($groups);
    }

    /**
     * Resolve users for signature blocks from raw array input.
     */
    public function resolveSignatures(array $data): array
    {
        $userIds = array_filter([
            'mengajukan'   => $data['mengajukan_id'] ?? null,
            'mengetahui_1' => $data['mengetahui_1_id'] ?? null,
            'mengetahui_2' => $data['mengetahui_2_id'] ?? null,
            'mengetahui_3' => $data['mengetahui_3_id'] ?? null,
            'mengetahui_4' => $data['mengetahui_4_id'] ?? null,
        ]);

        $users = ! empty($userIds)
            ? User::query()->whereIn('id', array_values($userIds))->get()->keyBy('id')
            : collect();

        return [
            'mengajukan'   => isset($data['mengajukan_id']) && $users->has($data['mengajukan_id']) ? $users->get($data['mengajukan_id']) : null,
            'mengetahui_1' => isset($data['mengetahui_1_id']) && $users->has($data['mengetahui_1_id']) ? $users->get($data['mengetahui_1_id']) : null,
            'mengetahui_2' => isset($data['mengetahui_2_id']) && $users->has($data['mengetahui_2_id']) ? $users->get($data['mengetahui_2_id']) : null,
            'mengetahui_3' => isset($data['mengetahui_3_id']) && $users->has($data['mengetahui_3_id']) ? $users->get($data['mengetahui_3_id']) : null,
            'mengetahui_4' => isset($data['mengetahui_4_id']) && $users->has($data['mengetahui_4_id']) ? $users->get($data['mengetahui_4_id']) : null,
        ];
    }

    /**
     * Render KKP Work Order PDF (Single or Batch).
     */
    public function renderKkpWorkOrder(WorkOrder $workOrder): StreamedResponse
    {
        return $this->renderBulkKkpWorkOrder(collect([$workOrder]));
    }

    /**
     * Render Bulk KKP Work Order PDF (Quarter A4 / A6).
     */
    public function renderBulkKkpWorkOrder(iterable $workOrders): StreamedResponse
    {
        $workOrders = $workOrders instanceof Collection ? $workOrders : collect($workOrders);

        $workOrders->loadMissing([
            'manufacturingOrder.workOrders.operation',
            'manufacturingOrder.workOrders.workCenter',
            'manufacturingOrder.product',
            'manufacturingOrder.uom',
            'manufacturingOrder.company',
            'operation',
            'workCenter',
            'product',
            'uom',
        ]);

        $pdf = Pdf::loadView('mrp_report::pdf.kkp-work-order', [
            'workOrders' => $workOrders,
            'service'    => $this,
        ]);

        $pdf->setPaper('a6', 'portrait');

        $filename = 'KKP-Work-Order-'.date('YmdHis').'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    /**
     * Render KKP Manufacturing Order PDF.
     */
    public function renderKkpManufacturingOrder(Order $order): StreamedResponse
    {
        return $this->renderBulkKkpManufacturingOrder(collect([$order]));
    }

    /**
     * Render Bulk KKP Manufacturing Order PDF (Quarter A4 / A6).
     */
    public function renderBulkKkpManufacturingOrder(iterable $orders): StreamedResponse
    {
        $orders = $orders instanceof Collection ? $orders : collect($orders);

        $orders->loadMissing([
            'workOrders.operation',
            'workOrders.workCenter',
            'product',
            'uom',
            'company',
        ]);

        $pdf = Pdf::loadView('mrp_report::pdf.kkp-manufacturing-order', [
            'orders'  => $orders,
            'service' => $this,
        ]);

        $pdf->setPaper('a6', 'portrait');

        $filename = 'KKP-Manufacturing-Order-'.date('YmdHis').'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    /**
     * Render BoM Report Benang PDF.
     */
    public function renderBomBenang(iterable $orders, array $signatureData = []): StreamedResponse
    {
        $orders = $orders instanceof Collection ? $orders : collect($orders);
        $components = $this->getBenangComponents($orders);
        $signatures = $this->resolveSignatures($signatureData);

        $moNames = $orders->pluck('name')->filter()->implode(', ');

        $pdf = Pdf::loadView('mrp_report::pdf.bom-report-benang', [
            'orders'     => $orders,
            'components' => $components,
            'moNames'    => $moNames,
            'signatures' => $signatures,
            'service'    => $this,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'BoM-Report-Benang-'.date('YmdHis').'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    /**
     * Render Accessories BoM Report PDF.
     */
    public function renderAccessoriesBom(iterable $orders, array $signatureData = []): StreamedResponse
    {
        $orders = $orders instanceof Collection ? $orders : collect($orders);
        $components = $this->getAccessoriesComponents($orders);
        $signatures = $this->resolveSignatures($signatureData);

        $moNames = $orders->pluck('name')->filter()->implode(', ');
        $productNames = $orders->map(fn ($o) => $o->product?->name)->filter()->unique()->implode(', ');
        $totalQuantity = $orders->sum('quantity');

        // Resolve source SO names
        $soNames = $orders->map(fn ($o) => $this->getSourceName($o))->unique()->implode(', ');

        $pdf = Pdf::loadView('mrp_report::pdf.bom-report-accessories', [
            'orders'        => $orders,
            'components'    => $components,
            'moNames'       => $moNames,
            'productNames'  => $productNames,
            'totalQuantity' => $totalQuantity,
            'soNames'       => $soNames,
            'signatures'    => $signatures,
            'service'       => $this,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Accessories-BoM-Report-'.date('YmdHis').'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    /**
     * Render Report Bon Aksesoris PDF (Grouped by Sales Order).
     */
    public function renderBonAksesoris(iterable $orders, array $signatureData = []): StreamedResponse
    {
        $orders = $orders instanceof Collection ? $orders : collect($orders);
        $groupedData = $this->getBonAksesorisGroupedData($orders);
        $signatures = $this->resolveSignatures($signatureData);

        $pdf = Pdf::loadView('mrp_report::pdf.bon-aksesoris', [
            'groups'     => $groupedData,
            'signatures' => $signatures,
            'service'    => $this,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Bon-Aksesoris-Produksi-'.date('YmdHis').'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    /**
     * Render Production Order MSP PDF (Standard MO layout + Benang components + Signatures).
     */
    public function renderProductionOrderMsp(Order $order, array $signatureData = []): StreamedResponse
    {
        $order->loadMissing([
            'workOrders.operation',
            'workOrders.workCenter',
            'product',
            'uom',
            'assignedUser',
            'company',
        ]);

        $benangComponents = $this->getBenangComponents(collect([$order]));
        $signatures = $this->resolveSignatures($signatureData);

        $pdf = Pdf::loadView('mrp_report::pdf.production-order-msp', [
            'order'            => $order,
            'benangComponents' => $benangComponents,
            'signatures'       => $signatures,
            'service'          => $this,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Production-Order-MSP-'.str_replace('/', '_', $order->name).'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
