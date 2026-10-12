<?php

return [
    'navigation' => [
        'title' => 'Component Switches',
    ],
    'action' => [
        'label' => 'Switch Component',
    ],
    'modal' => [
        'heading'     => 'Switch Manufacturing Component',
        'description' => 'Replace an unavailable or depleted component on this Manufacturing Order with a substitute product without changing the master BoM.',
        'submit'      => 'Confirm Switch',
        'fields'      => [
            'original_component'   => 'Component to Replace',
            'replacement_product'  => 'Replacement Product',
            'replacement_quantity' => 'Quantity to Consume',
            'reason'               => 'Reason for Replacement',
        ],
        'placeholders' => [
            'reason' => 'e.g. Out of stock, emergency vendor substitution, approved by QC',
        ],
    ],
    'table' => [
        'columns' => [
            'date'                 => 'Date',
            'original_product'     => 'Original Component',
            'original_quantity'    => 'Original Qty',
            'replacement_product'  => 'Replacement Component',
            'replacement_quantity' => 'Replacement Qty',
            'reason'               => 'Reason',
            'switched_by'          => 'Switched By',
        ],
    ],
    'notifications' => [
        'switched_title' => 'Component Switched Successfully',
        'switched_body'  => 'Component %s has been substituted with %s.',
        'failed_title'   => 'Failed to Switch Component',
    ],
    'errors' => [
        'order_already_finished' => 'Cannot switch components on an order that is already completed or cancelled.',
        'invalid_move'           => 'The specified stock move does not belong to this manufacturing order.',
        'move_already_closed'    => 'Cannot switch a component that has already been consumed or cancelled.',
        'invalid_quantity'       => 'Replacement quantity must be greater than zero.',
    ],
];
