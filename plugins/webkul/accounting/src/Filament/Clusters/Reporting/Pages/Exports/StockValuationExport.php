<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockValuationExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    protected iterable $records;

    protected string $currencyCode;

    protected int $rowCount = 0;

    public function __construct(iterable $records, string $currencyCode = 'IDR')
    {
        $this->records = $records;
        $this->currencyCode = $currencyCode;
    }

    public function headings(): array
    {
        return [
            ['Stock Valuation Report - '.Carbon::now()->format('d M Y H:i')],
            ['Currency: '.$this->currencyCode],
            [],
            [
                'SKU / Ref',
                'Product Name',
                'Category',
                'Warehouse',
                'Location',
                'On Hand Qty',
                'UoM',
                'Unit Cost',
                'Total Stock Value',
            ],
        ];
    }

    public function array(): array
    {
        $rows = [];
        $totalQty = 0;
        $totalValuation = 0;

        foreach ($this->records as $record) {
            $qty = (float) $record->quantity;
            $cost = (float) ($record->product?->cost ?? 0);
            $totalVal = $qty * $cost;

            $totalQty += $qty;
            $totalValuation += $totalVal;

            $rows[] = [
                $record->product?->reference ?? '—',
                $record->product?->name ?? 'Unknown',
                $record->product?->category?->name ?? '—',
                $record->location?->warehouse?->name ?? '—',
                $record->location?->full_name ?? $record->location?->name ?? '—',
                $qty,
                $record->product?->uom?->name ?? '—',
                $cost,
                $totalVal,
            ];
        }

        // Summary row
        $rows[] = [
            'TOTAL',
            '',
            '',
            '',
            '',
            $totalQty,
            '',
            '',
            $totalValuation,
        ];

        $this->rowCount = count($rows);

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 35,
            'C' => 20,
            'D' => 20,
            'E' => 25,
            'F' => 15,
            'G' => 10,
            'H' => 18,
            'I' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $this->rowCount + 4; // 4 rows of headings and blank line

        $styles = [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => '1F2937'],
                ],
            ],
            2 => [
                'font' => [
                    'italic' => true,
                    'size' => 10,
                    'color' => ['rgb' => '6B7280'],
                ],
            ],
            4 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];

        // Format data rows
        if ($this->rowCount > 0) {
            $sheet->getStyle("F5:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("H5:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("I5:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("F5:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("H5:H{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("I5:I{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');

            // Total row style
            $styles[$lastRow] = [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                    ],
                ],
            ];
        }

        return $styles;
    }
}
