<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Valuation Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 10px;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }
        .stats-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-box {
            padding: 10px 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .stats-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .stats-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
        }
        table.data-table td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .total-row {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            border-top: 2px solid #0f172a;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Stock Valuation Report</h1>
        <div class="subtitle">Printed on {{ $date }} | Currency: {{ $currency_code }}</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td style="width: 33.33%; padding-right: 10px;">
                <div class="stats-box">
                    <div class="stats-label">Total Stock Value</div>
                    <div class="stats-value" style="color: #059669;">
                        {{ $currency_code }} {{ number_format($stats['total_valuation'], 2, ',', '.') }}
                    </div>
                </div>
            </td>
            <td style="width: 33.33%; padding-right: 10px;">
                <div class="stats-box">
                    <div class="stats-label">Total Units In Stock</div>
                    <div class="stats-value" style="color: #2563eb;">
                        {{ number_format($stats['total_units'], 2, ',', '.') }}
                    </div>
                </div>
            </td>
            <td style="width: 33.33%;">
                <div class="stats-box">
                    <div class="stats-label">Total Products (SKUs)</div>
                    <div class="stats-value" style="color: #4f46e5;">
                        {{ number_format($stats['total_products'], 0, ',', '.') }} SKU
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">SKU / Ref</th>
                <th style="width: 25%;">Product Name</th>
                <th style="width: 14%;">Category</th>
                <th style="width: 14%;">Warehouse / Location</th>
                <th class="text-right" style="width: 10%;">On Hand</th>
                <th class="text-center" style="width: 5%;">UoM</th>
                <th class="text-right" style="width: 10%;">Unit Cost</th>
                <th class="text-right" style="width: 10%;">Total Value</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumQty = 0;
                $sumVal = 0;
            @endphp
            @foreach ($records as $record)
                @php
                    $qty = (float) $record->quantity;
                    $cost = (float) ($record->product?->cost ?? 0);
                    $total = $qty * $cost;
                    $sumQty += $qty;
                    $sumVal += $total;
                @endphp
                <tr>
                    <td>{{ $record->product?->reference ?? '—' }}</td>
                    <td class="font-bold">{{ $record->product?->name ?? 'Unknown' }}</td>
                    <td>{{ $record->product?->category?->name ?? '—' }}</td>
                    <td>{{ $record->location?->warehouse?->name ? $record->location->warehouse->name . ' - ' : '' }}{{ $record->location?->name ?? '—' }}</td>
                    <td class="text-right">{{ number_format($qty, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $record->product?->uom?->name ?? '—' }}</td>
                    <td class="text-right">{{ number_format($cost, 2, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="font-bold">TOTAL</td>
                <td class="text-right">{{ number_format($sumQty, 2, ',', '.') }}</td>
                <td></td>
                <td></td>
                <td class="text-right">{{ number_format($sumVal, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Generated automatically by ERP System | Page printed at {{ now()->toDateTimeString() }}
    </div>
</body>
</html>
