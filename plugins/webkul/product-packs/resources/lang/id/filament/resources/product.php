<?php

return [
    'pack_section' => [
        'title'       => 'Paket Produk (Product Pack)',
        'description' => 'Konfigurasikan produk ini sebagai paket gabungan dari beberapa produk/komponen.',
    ],
    'fields' => [
        'is_pack'                      => 'Paket Produk?',
        'is_pack_short'                => 'Paket',
        'is_pack_helper'               => 'Centang jika produk ini merupakan bundel/paket dari beberapa produk lain.',
        'pack_type'                    => 'Tipe Tampilan Paket',
        'pack_type_helper'             => 'Detail: Tampilkan komponen satu per satu di pesanan. Non-Detail: Hanya tampilkan produk paket.',
        'pack_component_price'         => 'Harga Komponen Paket',
        'pack_component_price_helper'  => 'Metode perhitungan harga komponen di pesanan penjualan (Sales Order).',
        'pack_modifiable'              => 'Komponen Dapat Diubah',
        'pack_modifiable_helper'       => 'Izinkan pengguna mengubah atau menghapus komponen paket saat di Sales Order.',
        'pack_lines'                   => 'Daftar Komponen Paket',
        'component_product'            => 'Produk Komponen',
        'quantity'                     => 'Jumlah / Qty',
        'unit_price'                   => 'Harga Satuan',
    ],
    'actions' => [
        'add_component' => 'Tambah Komponen',
    ],
];
