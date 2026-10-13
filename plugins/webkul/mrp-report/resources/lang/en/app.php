<?php

return [
    'plugin' => [
        'name'        => 'Mrp Report',
        'description' => 'Comprehensive manufacturing reports for Aureus ERP',
    ],

    'navigation' => [
        'reports' => 'Mrp Reports',
    ],

    'reports' => [
        'kkp_wo' => [
            'title'       => 'KKP Work Order',
            'label'       => 'KKP Work Order',
            'description' => 'A4 quarter card report for Work Order operations with barcode and checklist',
        ],
        'kkp_mo' => [
            'title'       => 'KKP Manufacturing Order',
            'label'       => 'KKP Manufacturing Order',
            'description' => 'A4 quarter card report for Manufacturing Order with barcode and operation details',
        ],
        'bom_benang' => [
            'title'       => 'BoM Report Benang',
            'label'       => 'BoM Report Benang',
            'modal_title' => 'Print BoM Report Benang (Yarn Summary)',
            'description' => 'Yarn materials requirement summary report with verification signatures',
        ],
        'accessories_bom' => [
            'title'       => 'Accessories BoM Report',
            'label'       => 'Accessories BoM Report',
            'modal_title' => 'Print Accessories BoM Report',
            'description' => 'Accessories requirements recap report with authorization signatures',
        ],
        'bon_aksesoris' => [
            'title'       => 'Report Bon Aksesoris',
            'label'       => 'Report Bon Aksesoris',
            'modal_title' => 'Print Bon Aksesoris Report (Grouped by Sales Order)',
            'description' => 'Accessories issuance slip grouped by Sales Order',
        ],
        'production_order_msp' => [
            'title'       => 'Production Order (MSP)',
            'label'       => 'Production Order (MSP)',
            'modal_title' => 'Print Production Order with Yarn Components and Signatures',
            'description' => 'Standard manufacturing order layout customized with Yarn components and 3-tier approvals',
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
        'print'      => 'Print PDF',
        'download'   => 'Download PDF',
        'bulk_print' => 'Print Selected',
    ],

    'labels' => [
        'manufacturing_order' => 'Manufacturing Order',
        'sales_order'         => 'Sales Order',
        'customer'            => 'Customer',
        'product'             => 'Product',
        'size_steam'          => 'Size Steam',
        'delivery'            => 'Delivery',
        'qty_to_produce'      => 'Quantity to Produce',
        'mark_as_done'        => 'Mark As Done',
        'operations'          => 'Operations / Work Orders',
        'operation'           => 'Operation',
        'work_center'         => 'Work Center',
        'done'                => 'Done',
        'current'             => 'Current',
        'source'              => 'Source',
        'document_id'         => 'Document ID',
        'page'                => 'Page',
        'revision'            => 'Revision',
        'revision_no'         => 'Revision No',
        'effective_date'      => 'Effective Date',
        'source_ref'          => 'Source Ref',
        'bill_of_materials'   => 'BILL OF MATERIALS',
        'product_to_consume'  => 'Product to Consume',
        'quantity'            => 'Qty',
        'uom'                 => 'UoM',
        'category'            => 'Category',
        'no_components_found' => 'No components found for selected Manufacturing Orders.',
        'total_quantity'      => 'Total Quantity',
        'without_so'          => 'Without SO',
    ],
];
