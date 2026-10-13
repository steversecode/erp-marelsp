<?php

return [
    'plugin' => [
        'name'        => 'Mrp Report',
        'description' => 'Laporan manufaktur terintegrasi untuk Aureus ERP',
    ],

    'navigation' => [
        'reports' => 'Laporan MRP',
    ],

    'reports' => [
        'kkp_wo' => [
            'title'       => 'KKP Work Order',
            'label'       => 'KKP Work Order',
            'description' => 'Kartu Kerja Produksi ukuran 1/4 A4 untuk Work Order dengan barcode dan checklist operasi',
        ],
        'kkp_mo' => [
            'title'       => 'KKP Manufacturing Order',
            'label'       => 'KKP Manufacturing Order',
            'description' => 'Kartu Kerja Produksi ukuran 1/4 A4 untuk Manufacturing Order dengan barcode dan ringkasan order',
        ],
        'bom_benang' => [
            'title'       => 'BoM Report Benang',
            'label'       => 'BoM Report Benang',
            'modal_title' => 'Cetak Rekap Kebutuhan Benang Produksi',
            'description' => 'Rekapitulasi kebutuhan benang produksi dengan tanda tangan PPIC, Purchasing, dan Gudang',
        ],
        'accessories_bom' => [
            'title'       => 'Accessories BoM Report',
            'label'       => 'Accessories BoM Report',
            'modal_title' => 'Cetak Rekap Kebutuhan Aksesoris Produksi',
            'description' => 'Rekapitulasi kebutuhan aksesoris produksi dengan otorisasi tanda tangan bertingkat',
        ],
        'bon_aksesoris' => [
            'title'       => 'Report Bon Aksesoris',
            'label'       => 'Report Bon Aksesoris',
            'modal_title' => 'Cetak Bon Aksesoris Produksi (Dikelompokkan per Sales Order)',
            'description' => 'Bon pengeluaran aksesoris produksi yang dikelompokkan berdasarkan Sales Order',
        ],
        'production_order_msp' => [
            'title'       => 'Production Order (MSP)',
            'label'       => 'Production Order (MSP)',
            'modal_title' => 'Cetak Production Order dengan Komponen Benang dan Tanda Tangan',
            'description' => 'Layout Production Order khusus dengan filter komponen Benang KK dan tanda tangan otorisasi',
        ],
    ],

    'signatures' => [
        'mengajukan'   => 'Mengajukan (PPIC / Admin)',
        'purchasing'   => 'Purchasing',
        'gudang'       => 'Gudang',
        'gm'           => 'General Manager',
        'mengetahui_1' => 'Mengetahui 1 (Purchasing)',
        'mengetahui_2' => 'Mengetahui 2 (Gudang)',
        'mengetahui_3' => 'Mengetahui 3 (General Manager)',
        'mengetahui_4' => 'Mengetahui 4',
    ],

    'actions' => [
        'print'      => 'Cetak PDF',
        'download'   => 'Unduh PDF',
        'bulk_print' => 'Cetak Terpilih',
    ],

    'labels' => [
        'manufacturing_order' => 'Manufacturing Order',
        'sales_order'         => 'Sales Order',
        'customer'            => 'Customer',
        'product'             => 'Produk',
        'size_steam'          => 'Size Steam',
        'delivery'            => 'Delivery',
        'qty_to_produce'      => 'Jumlah Produksi',
        'mark_as_done'        => 'Mark As Done',
        'operations'          => 'Operasi / Work Orders',
        'operation'           => 'Operasi',
        'work_center'         => 'Work Center',
        'done'                => 'Selesai',
        'current'             => 'Saat Ini',
        'source'              => 'Sumber',
        'document_id'         => 'Document ID',
        'page'                => 'Halaman',
        'revision'            => 'Revisi',
        'revision_no'         => 'No Revisi',
        'effective_date'      => 'Tanggal Efektif',
        'source_ref'          => 'Source Ref',
        'bill_of_materials'   => 'BILL OF MATERIALS',
        'product_to_consume'  => 'Produk / Komponen',
        'quantity'            => 'Qty',
        'uom'                 => 'Satuan',
        'category'            => 'Kategori',
        'no_components_found' => 'Tidak ada komponen yang ditemukan untuk Manufacturing Order terpilih.',
        'total_quantity'      => 'Total Kuantitas',
        'without_so'          => 'Tanpa SO',
    ],
];
