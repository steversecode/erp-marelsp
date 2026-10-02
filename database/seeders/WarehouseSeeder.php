<?php

namespace Database\Seeders;

use App\Models\StockLocation;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. Root View Locations (Odoo Standard)
        // ==========================================
        $physicalRoot = StockLocation::updateOrCreate(
            ['code' => 'PHYSICAL'],
            [
                'name' => 'Physical Locations',
                'type' => 'view',
                'parent_id' => null,
                'is_active' => true,
            ]
        );

        $partnerRoot = StockLocation::updateOrCreate(
            ['code' => 'PARTNER'],
            [
                'name' => 'Partner Locations',
                'type' => 'view',
                'parent_id' => null,
                'is_active' => true,
            ]
        );

        $virtualRoot = StockLocation::updateOrCreate(
            ['code' => 'VIRTUAL'],
            [
                'name' => 'Virtual Locations',
                'type' => 'view',
                'parent_id' => null,
                'is_active' => true,
            ]
        );

        // ==========================================
        // 2. Partner & Virtual Sub-Locations
        // ==========================================
        StockLocation::updateOrCreate(
            ['code' => 'VENDOR-SUPP'],
            [
                'name' => 'Vendors',
                'type' => 'vendor',
                'parent_id' => $partnerRoot->id,
                'address' => 'Supplier / External Partner Logistik',
                'is_active' => true,
            ]
        );

        StockLocation::updateOrCreate(
            ['code' => 'PARTNER-CUST'],
            [
                'name' => 'Customers',
                'type' => 'customer',
                'parent_id' => $partnerRoot->id,
                'address' => 'Pelanggan / Pembeli',
                'is_active' => true,
            ]
        );

        StockLocation::updateOrCreate(
            ['code' => 'PROD-FLOOR'],
            [
                'name' => 'Production',
                'type' => 'production',
                'parent_id' => $virtualRoot->id,
                'address' => 'Lantai Kerja Rajut & Jahit PT Marel',
                'is_active' => true,
            ]
        );

        StockLocation::updateOrCreate(
            ['code' => 'SCRAP-LOSS'],
            [
                'name' => 'Scrap',
                'type' => 'loss',
                'is_scrap' => true,
                'parent_id' => $virtualRoot->id,
                'address' => 'Penampungan Limbah / Afval',
                'is_active' => true,
            ]
        );

        StockLocation::updateOrCreate(
            ['code' => 'INV-LOSS'],
            [
                'name' => 'Inventory adjustment',
                'type' => 'loss',
                'parent_id' => $virtualRoot->id,
                'address' => 'Selisih Stok Opname',
                'is_active' => true,
            ]
        );

        StockLocation::updateOrCreate(
            ['code' => 'SUBCON-DYEING'],
            [
                'name' => 'Subcontracting (Dyeing)',
                'type' => 'dyeing_subcon',
                'parent_id' => $virtualRoot->id,
                'address' => 'Kawasan Industri Rancaekek',
                'is_active' => true,
            ]
        );

        StockLocation::updateOrCreate(
            ['code' => 'VIRTUAL-SAMPLE'],
            [
                'name' => 'Sample / R&D',
                'type' => 'sample',
                'parent_id' => $virtualRoot->id,
                'address' => 'Studio Desain & Sample',
                'is_active' => true,
            ]
        );

        StockLocation::updateOrCreate(
            ['code' => 'TRANSIT-WH'],
            [
                'name' => 'Inter-warehouse Transit',
                'type' => 'transit',
                'parent_id' => $virtualRoot->id,
                'address' => 'Lokasi Transit Antar-Gudang',
                'is_active' => true,
            ]
        );

        // ==========================================
        // 3. PT Marel Sukses Pratama (Main WH)
        // ==========================================
        $whView = StockLocation::updateOrCreate(
            ['code' => 'WH-VIEW'],
            [
                'name' => 'WH',
                'type' => 'view',
                'parent_id' => $physicalRoot->id,
                'is_active' => true,
            ]
        );

        $whStock = StockLocation::updateOrCreate(
            ['code' => 'WH/Stock'],
            [
                'name' => 'Stock',
                'type' => 'internal',
                'parent_id' => $whView->id,
                'address' => 'PT Marel Sukses Pratama',
                'is_active' => true,
            ]
        );

        // Sub-locations of WH/Stock
        StockLocation::updateOrCreate(
            ['code' => 'WH-MAIN'],
            [
                'name' => 'Gudang Bahan Baku Utama',
                'type' => 'internal',
                'parent_id' => $whStock->id,
                'address' => 'Gedung A Lantai 1',
                'is_active' => true,
            ]
        );

        StockLocation::updateOrCreate(
            ['code' => 'WH-FG'],
            [
                'name' => 'Gudang Barang Jadi (Finished Goods)',
                'type' => 'internal',
                'parent_id' => $whStock->id,
                'address' => 'Gedung B',
                'is_active' => true,
            ]
        );

        $whMarel = Warehouse::updateOrCreate(
            ['code' => 'WH'],
            [
                'name' => 'PT Marel Sukses Pratama',
                'address' => 'PT Marel Sukses Pratama',
                'view_location_id' => $whView->id,
                'lot_stock_id' => $whStock->id,
                'incoming_steps' => '1_step',
                'outgoing_steps' => '1_step',
                'buy_to_resupply' => true,
                'manufacture_to_resupply' => true,
                'manufacture_steps' => '2_steps',
                'resupply_from_warehouse_ids' => null,
                'is_active' => true,
            ]
        );
        $whView->update(['warehouse_id' => $whMarel->id]);
        $whStock->update(['warehouse_id' => $whMarel->id]);
        StockLocation::where('code', 'WH-MAIN')->update(['warehouse_id' => $whMarel->id]);
        StockLocation::where('code', 'WH-FG')->update(['warehouse_id' => $whMarel->id]);

        // ==========================================
        // 4. Online Shop (wh-on)
        // ==========================================
        $whOnView = StockLocation::updateOrCreate(
            ['code' => 'wh-on-VIEW'],
            [
                'name' => 'wh-on',
                'type' => 'view',
                'parent_id' => $physicalRoot->id,
                'is_active' => true,
            ]
        );

        $whOnStock = StockLocation::updateOrCreate(
            ['code' => 'wh-on/Stock'],
            [
                'name' => 'Stock',
                'type' => 'internal',
                'parent_id' => $whOnView->id,
                'address' => 'PT Marel Sukses Pratama',
                'is_active' => true,
            ]
        );

        $whOnline = Warehouse::updateOrCreate(
            ['code' => 'wh-on'],
            [
                'name' => 'Online Shop',
                'address' => 'PT Marel Sukses Pratama',
                'view_location_id' => $whOnView->id,
                'lot_stock_id' => $whOnStock->id,
                'incoming_steps' => '1_step',
                'outgoing_steps' => '1_step',
                'buy_to_resupply' => true,
                'manufacture_to_resupply' => false,
                'manufacture_steps' => '1_step',
                'resupply_from_warehouse_ids' => [$whMarel->id],
                'is_active' => true,
            ]
        );
        $whOnView->update(['warehouse_id' => $whOnline->id]);
        $whOnStock->update(['warehouse_id' => $whOnline->id]);

        // ==========================================
        // 5. Marelika (WH2M)
        // ==========================================
        $wh2mView = StockLocation::updateOrCreate(
            ['code' => 'WH2M-VIEW'],
            [
                'name' => 'WH2M',
                'type' => 'view',
                'parent_id' => $physicalRoot->id,
                'is_active' => true,
            ]
        );

        $wh2mStock = StockLocation::updateOrCreate(
            ['code' => 'WH2M/Stock'],
            [
                'name' => 'Stock',
                'type' => 'internal',
                'parent_id' => $wh2mView->id,
                'address' => 'Dua Marelika Gemilang',
                'is_active' => true,
            ]
        );

        $whMarelika = Warehouse::updateOrCreate(
            ['code' => 'WH2M'],
            [
                'name' => 'Marelika',
                'address' => 'Dua Marelika Gemilang',
                'view_location_id' => $wh2mView->id,
                'lot_stock_id' => $wh2mStock->id,
                'incoming_steps' => '1_step',
                'outgoing_steps' => '1_step',
                'buy_to_resupply' => true,
                'manufacture_to_resupply' => false,
                'manufacture_steps' => '1_step',
                'resupply_from_warehouse_ids' => [$whMarel->id],
                'is_active' => true,
            ]
        );
        $wh2mView->update(['warehouse_id' => $whMarelika->id]);
        $wh2mStock->update(['warehouse_id' => $whMarelika->id]);
    }
}
