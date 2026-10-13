<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>KKP Manufacturing Order</title>
    <style type="text/css">
        @page {
            size: 105mm 148.5mm portrait;
            margin: 6mm 6mm 6mm 6mm;
        }

        html, body, table, th, td, div, span, p, b, strong {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif !important;
        }

        body {
            font-size: 9px;
            color: #222222;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        .card-page {
            page-break-after: always;
            position: relative;
        }

        .card-page:last-child {
            page-break-after: auto;
        }

        .page-header {
            text-align: right;
            font-size: 8px;
            color: #666666;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .barcode-section {
            text-align: center;
            margin-bottom: 4px;
        }

        .barcode-section img {
            max-width: 180px;
            height: 35px;
        }

        .mo-section {
            text-align: center;
            margin-bottom: 6px;
        }

        .mo-label {
            font-size: 8px;
            color: #666666;
            margin-bottom: 1px;
        }

        .mo-value {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
            color: #111111;
        }

        .source-section {
            text-align: center;
            margin-bottom: 6px;
        }

        .source-label {
            font-size: 8px;
            color: #666666;
            margin-bottom: 1px;
        }

        .source-value {
            font-size: 10px;
            font-weight: bold;
            margin: 0;
        }

        .field-group {
            margin-bottom: 5px;
        }

        .field-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #333333;
            margin-bottom: 1px;
        }

        .field-value {
            font-size: 8.5px;
            color: #111111;
        }

        .sku-tag {
            font-size: 8px;
            font-weight: bold;
            color: #000000;
            margin-top: 1px;
        }

        .done-barcode-section {
            text-align: center;
            margin-top: 12px;
            margin-bottom: 8px;
        }

        .done-barcode-title {
            font-size: 8.5px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .done-barcode-section img {
            max-width: 140px;
            height: 25px;
        }

        .operations-section {
            margin-top: 6px;
        }

        .operations-title {
            font-size: 8px;
            font-weight: bold;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }

        .table-operations {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
        }

        .table-operations th {
            border: 1px solid #cccccc;
            background-color: #f2f2f2;
            padding: 2px 3px;
            text-align: left;
            font-weight: bold;
        }

        .table-operations td {
            border: 1px solid #cccccc;
            padding: 2px 3px;
            vertical-align: middle;
        }

        .col-done {
            width: 20px;
            text-align: center;
        }

        .box-checkbox {
            width: 9px;
            height: 9px;
            border: 1px solid #000000;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    @foreach ($orders as $index => $mo)
        @php
            $sourceName = $service->getSourceName($mo);
            $customerName = $service->getCustomerName($mo);
            $product = $mo->product;
            $productSize = $service->getProductSize($product);
            $deliveryDate = $service->getDeliveryDate($mo);
            $qty = (float) $mo->quantity;
            $uomName = $mo->uom?->name ?: 'Unit';
        @endphp

        <div class="card-page">
            <div class="page-header">
                Page {{ $index + 1 }} of {{ count($orders) }}
            </div>

            <!-- Barcode Section (Center) -->
            <div class="barcode-section">
                @if ($mo->name && class_exists(\Milon\Barcode\Facades\DNS1DFacade::class))
                    <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($mo->name, 'C128', 1.5, 40) }}" alt="{{ $mo->name }}" />
                @endif
            </div>

            <!-- Manufacturing Order -->
            <div class="mo-section">
                <div class="mo-label">Manufacturing Order:</div>
                <div class="mo-value">{{ $mo->name }}</div>
            </div>

            <!-- Source -->
            <div class="source-section">
                <div class="source-label">Source:</div>
                <div class="source-value">{{ $sourceName }}</div>
            </div>

            <!-- Size Steam -->
            @if ($productSize)
                <div class="field-group">
                    <div class="field-title">Size Steam:</div>
                    <div class="field-value">{{ $productSize }}</div>
                </div>
            @endif

            <!-- Customer -->
            <div class="field-group">
                <div class="field-title">Customer:</div>
                <div class="field-value">{{ $customerName ?: '-' }}</div>
            </div>

            <!-- Product -->
            <div class="field-group">
                <div class="field-title">Product:</div>
                <div class="field-value">
                    <div>{{ $product?->name ?: '-' }}</div>
                    @if ($product?->reference)
                        <div class="sku-tag">SKU: {{ $product->reference }}</div>
                    @endif
                </div>
            </div>

            <!-- Delivery -->
            <div class="field-group">
                <div class="field-title">Delivery:</div>
                <div class="field-value">{{ $deliveryDate ?: '-' }}</div>
            </div>

            <!-- Quantity to Produce -->
            <div class="field-group">
                <div class="field-title">Quantity to Produce:</div>
                <div class="field-value">{{ number_format($qty, 0) }} {{ $uomName }}</div>
            </div>

            <!-- Barcode Mark As Done -->
            <div class="done-barcode-section">
                <div class="done-barcode-title">Mark As Done</div>
                @if (class_exists(\Milon\Barcode\Facades\DNS1DFacade::class))
                    <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG('OMFDONE', 'C128', 1.2, 30) }}" alt="OMFDONE" />
                @endif
            </div>

            <!-- Operations List -->
            @if ($mo->workOrders && $mo->workOrders->isNotEmpty())
                <div class="operations-section">
                    <div class="operations-title">Operations / Work Orders:</div>
                    <table class="table-operations">
                        <thead>
                            <tr>
                                <th class="col-done">Done</th>
                                <th>Operation</th>
                                <th>Work Center</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mo->workOrders as $itemWo)
                                <tr>
                                    <td class="col-done">
                                        <div class="box-checkbox"></div>
                                    </td>
                                    <td>{{ $itemWo->operation?->name ?: $itemWo->name }}</td>
                                    <td>{{ $itemWo->workCenter?->name ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach
</body>
</html>
