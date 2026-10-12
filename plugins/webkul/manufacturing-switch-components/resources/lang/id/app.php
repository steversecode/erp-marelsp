<?php

return [
    'navigation' => [
        'title' => 'Penggantian Komponen',
    ],
    'action' => [
        'label' => 'Switch Component',
    ],
    'modal' => [
        'heading'     => 'Ganti Komponen (Switch Component)',
        'description' => 'Ganti komponen yang habis atau tidak tersedia pada Manufacturing Order ini dengan produk pengganti tanpa merubah master BoM.',
        'submit'      => 'Konfirmasi Penggantian',
        'fields'      => [
            'original_component'   => 'Komponen yang Ingin Diganti',
            'replacement_product'  => 'Produk Pengganti',
            'replacement_quantity' => 'Jumlah yang Digunakan',
            'reason'               => 'Alasan Penggantian',
        ],
        'placeholders' => [
            'reason' => 'Contoh: Stok di gudang kosong, disetujui tim QC, bahan alternatif darurat',
        ],
    ],
    'table' => [
        'columns' => [
            'date'                 => 'Waktu',
            'original_product'     => 'Komponen Asal',
            'original_quantity'    => 'Qty Asal',
            'replacement_product'  => 'Komponen Pengganti',
            'replacement_quantity' => 'Qty Pengganti',
            'reason'               => 'Alasan',
            'switched_by'          => 'Oleh',
        ],
    ],
    'notifications' => [
        'switched_title' => 'Komponen Berhasil Diganti',
        'switched_body'  => 'Komponen %s telah diganti dengan %s.',
        'failed_title'   => 'Gagal Mengganti Komponen',
    ],
    'errors' => [
        'order_already_finished' => 'Tidak dapat mengganti komponen pada pesanan yang sudah selesai atau dibatalkan.',
        'invalid_move'           => 'Stock move yang dipilih tidak terkait dengan manufacturing order ini.',
        'move_already_closed'    => 'Tidak dapat mengganti komponen yang sudah dikonsumsi atau sudah dibatalkan.',
        'invalid_quantity'       => 'Jumlah produk pengganti harus lebih besar dari 0.',
    ],
];
