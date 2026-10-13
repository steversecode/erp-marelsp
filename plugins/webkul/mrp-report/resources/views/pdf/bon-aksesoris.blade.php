<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Bon Aksesoris Produksi</title>
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

        .group-page {
            page-break-after: always;
        }

        .group-page:last-child {
            page-break-after: auto;
        }

        .title-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title-header h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            color: #111111;
            letter-spacing: 0.5px;
        }

        .meta-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .meta-label {
            width: 38%;
            color: #555555;
        }

        .meta-separator {
            width: 4%;
            text-align: center;
        }

        .meta-value {
            font-weight: 500;
            color: #111111;
        }

        .section-header {
            font-size: 12px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 8px;
            border-bottom: 1px solid #333333;
            padding-bottom: 3px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f8f9fa;
            border-top: 1px solid #bdc3c7;
            border-bottom: 2px solid #bdc3c7;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            color: #2c3e50;
        }

        .items-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .empty-row {
            padding: 25px !important;
            color: #7f8c8d;
            background-color: #fdfdfd;
            font-style: italic;
        }

        .signatures-section {
            margin-top: 40px;
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
            width: 50%;
        }

        .signatures-table td {
            text-align: center;
            font-size: 11px;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #000000;
            width: 60%;
            margin: 0 auto 4px auto;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10.5px;
        }

        .signature-title {
            font-size: 9.5px;
            color: #555555;
        }
    </style>
</head>
<body>
    @foreach ($groups as $gIndex => $group)
        @php
            $moList = collect($group['orders'])->pluck('name')->filter()->implode(', ');
            $productsList = implode(', ', $group['product_names']);
        @endphp

        <div class="group-page">
            <div class="title-header">
                <h2>BON AKSESORIS PRODUKSI</h2>
            </div>

            <!-- Group Meta Box -->
            <table class="meta-container">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                        <table class="meta-table">
                            <tr>
                                <td class="meta-label">Sales Order</td>
                                <td class="meta-separator">:</td>
                                <td class="meta-value"><strong>{{ $group['so_name'] }}</strong></td>
                            </tr>
                            <tr>
                                <td class="meta-label">Customer</td>
                                <td class="meta-separator">:</td>
                                <td class="meta-value">{{ $group['customer_name'] }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Production Orders</td>
                                <td class="meta-separator">:</td>
                                <td class="meta-value">{{ $moList ?: '-' }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding-left: 15px;">
                        <table class="meta-table">
                            <tr>
                                <td class="meta-label">Page</td>
                                <td class="meta-separator">:</td>
                                <td class="meta-value">{{ $gIndex + 1 }} / {{ count($groups) }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Products</td>
                                <td class="meta-separator">:</td>
                                <td class="meta-value">{{ $productsList ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Total Quantity</td>
                                <td class="meta-separator">:</td>
                                <td class="meta-value">{{ number_format($group['total_quantity'], 0) }} Units</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Bill of Materials Section -->
            <div class="section-header">BILL OF MATERIALS</div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Product to Consume</th>
                        <th class="text-right" style="width: 18%;">Qty</th>
                        <th class="text-center" style="width: 15%;">UoM</th>
                        <th class="text-center" style="width: 22%;">Category</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($group['components'] as $comp)
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
                            <td class="text-center">{{ $comp['category'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center empty-row">
                                Tidak ada komponen aksesoris untuk Sales Order ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Signatures Section -->
            <div class="signatures-section">
                <table class="signatures-table">
                    <thead>
                        <tr>
                            <th>Mengajukan</th>
                            <th>Mengetahui</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-name">{{ $signatures['mengajukan']?->name ?? 'Admin / PPIC' }}</div>
                                <div class="signature-title">(PPIC)</div>
                            </td>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-name">{{ $signatures['mengetahui_1']?->name ?? 'Purchasing' }}</div>
                                <div class="signature-title">(Purchasing)</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</body>
</html>
