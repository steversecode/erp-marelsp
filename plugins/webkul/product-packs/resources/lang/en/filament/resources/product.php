<?php

return [
    'pack_section' => [
        'title'       => 'Product Pack',
        'description' => 'Configure this product as a pack of multiple components.',
    ],
    'fields' => [
        'is_pack'                      => 'Is a Pack?',
        'is_pack_short'                => 'Pack',
        'is_pack_helper'               => 'Check if this product consists of multiple bundled products.',
        'pack_type'                    => 'Pack Display Type',
        'pack_type_helper'             => 'Detailed: Show components individually on orders. Non Detailed: Single line for the pack.',
        'pack_component_price'         => 'Pack Component Price',
        'pack_component_price_helper'  => 'How component pricing is calculated on sales orders.',
        'pack_modifiable'              => 'Pack Modifiable',
        'pack_modifiable_helper'       => 'Allow sales reps to edit/delete components on sales order lines.',
        'pack_lines'                   => 'Pack Components',
        'component_product'            => 'Component Product',
        'quantity'                     => 'Quantity',
        'unit_price'                   => 'Unit Price',
    ],
    'actions' => [
        'add_component' => 'Add Component',
    ],
];
