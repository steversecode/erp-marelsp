<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Production Order MSP - {{ $order->name }}</title>
    <style type="text/css">
        @page {
            size: a4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        html, body, table, th, td, div, span, p, b, strong {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif !important;
        }

        body {
            font-size: 11px;
            color: #2c3e50;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-bar {
            width: 100%;
            border-bottom: 1px solid #cccccc;
            padding-bottom: 6px;
            margin-bottom: 15px;
            font-size: 10px;
            color: #666666;
        }

        .header-bar table {
            width: 100%;
        }

        .title-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .slip-title {
            font-size: 24px;
            font-weight: bold;
            color: #111111;
        }

        .meta-grid {
            width: 100%;
            margin-bottom: 20px;
        }

        .meta-grid td {
            width: 25%;
            vertical-align: top;
            padding: 4px 6px;
        }

        .meta-label {
            font-weight: bold;
            color: #555555;
            font-size: 10px;
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: 11px;
            color: #111111;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #111111;
            margin: 20px 0 8px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #333333;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #f8f9fa;
            border-top: 1px solid #bdc3c7;
            border-bottom: 2px solid #bdc3c7;
            padding: 7px 6px;
            text-align: left;
            font-weight: bold;
        }

        .data-table td {
            padding: 6px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .signatures-section {
            margin-top: 45px;
            page-break-inside: avoid;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatures-table th {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            padding-bottom: 55px;
            width: 33.33%;
        }

        .signatures-table td {
            text-align: center;
            font-size: 11px;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #000000;
            width: 75%;
            margin: 0 auto 4px auto;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10.5px;
        }

        .signature-title {
            font-size: 9.5px;
            color: #555555;
            font-weight: bold;
        }
    </style>
<body>
@php
    $items = isset($ordersData) ? $ordersData : [['order' => $order, 'benangComponents' => $benangComponents]];
@endphp

@foreach ($items as $idx => $item)
@php
    $order = $item['order'];
    $benangComponents = $item['benangComponents'];
@endphp
<div class="page-container" style="{{ $idx < count($items) - 1 ? 'page-break-after: always;' : '' }}">
    <!-- Top Bar -->
    <div class="header-bar">
        <table>
            <tr>
                <td>{{ now()->format('d/m/Y H:i') }}</td>
                <td style="text-align: center;">{{ $order->company?->name ?? 'PT Marel Sukses Pratama' }}</td>
                <td style="text-align: right;">1 / 1</td>
            </tr>
        </table>
    </div>

    <!-- Title & Barcode -->
    <table class="title-table">
        <tr>
            <td style="vertical-align: bottom;">
                <div class="slip-title">{{ $order->name }}</div>
            </td>
            <td style="text-align: right; vertical-align: bottom; width: 220px;">
                @if ($order->name && class_exists(\Milon\Barcode\Facades\DNS1DFacade::class))
                    <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($order->name, 'C128', 1.4, 38) }}" alt="{{ $order->name }}" />
                @endif
            </td>
        </tr>
    </table>

    <!-- Meta Details Grid -->
    <table class="meta-grid">
        <tr>
            <td>
                <div class="meta-label">Source:</div>
                <div class="meta-value">{{ $service->getSourceName($order) }}</div>
            </td>
            <td>
                <div class="meta-label">Responsible:</div>
                <div class="meta-value">{{ $order->assignedUser?->name ?? '-' }}</div>
            </td>
            <td>
                <div class="meta-label">Deadline:</div>
                <div class="meta-value">{{ $order->deadline_at ? $order->deadline_at->format('d/m/Y') : '-' }}</div>
            </td>
            <td>
                <div class="meta-label">Customer:</div>
                <div class="meta-value">{{ $service->getCustomerName($order) ?: '-' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="meta-label">Product:</div>
                <div class="meta-value"><strong>{{ $order->product?->name }}</strong></div>
            </td>
            <td>
                <div class="meta-label">SKU / Reference:</div>
                <div class="meta-value">{{ $order->product?->reference ?: '-' }}</div>
            </td>
            <td>
                <div class="meta-label">Quantity to Produce:</div>
                <div class="meta-value"><strong>{{ number_format((float) $order->quantity, 0) }} {{ $order->uom?->name }}</strong></div>
            </td>
            <td>
                <div class="meta-label">Delivery Date:</div>
                <div class="meta-value">{{ $service->getDeliveryDate($order) ?: '-' }}</div>
            </td>
        </tr>
    </table>

    <!-- Operations / Work Orders -->
    @if ($order->workOrders && $order->workOrders->isNotEmpty())
        <div class="section-title">Operations</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Operation</th>
                    <th style="width: 30%;">Work Center</th>
                    <th class="text-right" style="width: 15%;">Duration</th>
                    <th class="text-center" style="width: 20%;">Barcode</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->workOrders as $wo)
                    <tr>
                        <td style="font-weight: 500;">{{ $wo->operation?->name ?: $wo->name }}</td>
                        <td>{{ $wo->workCenter?->name ?: '-' }}</td>
                        <td class="text-right">{{ number_format((float) $wo->expected_duration, 0) }} min</td>
                        <td class="text-center">
                            @if ($wo->name && class_exists(\Milon\Barcode\Facades\DNS1DFacade::class))
                                <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($wo->name, 'C128', 1.0, 20) }}" alt="{{ $wo->name }}" />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Components (Benang KK) -->
    <div class="section-title">Components (Benang KK)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50%;">Product</th>
                <th class="text-right" style="width: 25%;">To Consume</th>
                <th class="text-center" style="width: 25%;">UoM</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($benangComponents as $comp)
                <tr>
                    <td style="font-weight: 500;">
                        {{ $comp['name'] }}
                        @if (! empty($comp['reference']))
                            <span style="font-size: 8.5px; color: #666666;">[{{ $comp['reference'] }}]</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: 500;">
                        {{ number_format($comp['qty'], 4) }}
                    </td>
                    <td class="text-center">{{ $comp['uom'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="padding: 15px; color: #7f8c8d; font-style: italic;">
                        Tidak ada komponen Benang KK untuk Manufacturing Order ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Approval Signatures -->
    <div class="signatures-section">
        <table class="signatures-table">
            <thead>
                <tr>
                    <th>Mengetahui</th>
                    <th>Diperiksa Oleh</th>
                    <th>Approved By</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $signatures['mengajukan']?->name ?? 'PPIC' }}</div>
                        <div class="signature-title">(PPIC)</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $signatures['mengetahui_1']?->name ?? 'Gudang' }}</div>
                        <div class="signature-title">(Gudang)</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $signatures['mengetahui_2']?->name ?? 'General Manager' }}</div>
                        <div class="signature-title">(General Manager)</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endforeach
</body>
</html>
