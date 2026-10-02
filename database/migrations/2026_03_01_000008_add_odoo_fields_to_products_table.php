<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Product Image (Avatar)
            $table->string('image', 255)->nullable()->after('name');

            // Product Type (Goods, Service, Combo)
            $table->string('type', 30)->default('goods')->change();

            // General Odoo Properties
            $table->string('barcode', 100)->nullable()->after('code');
            $table->boolean('can_be_sold')->default(true)->after('is_active');
            $table->boolean('can_be_purchased')->default(true)->after('can_be_sold');
            $table->boolean('can_be_manufactured')->default(false)->after('can_be_purchased');
            $table->boolean('can_be_subcontracted')->default(false)->after('can_be_manufactured');
            
            // Pricing, Invoicing Policy & UoM
            $table->decimal('sale_price', 15, 2)->default(0)->after('cost_price');
            $table->string('invoicing_policy', 30)->default('ordered')->after('sale_price'); // 'ordered' (Ordered quantities), 'delivered' (Delivered quantities)
            $table->string('uom_po', 20)->nullable()->after('uom'); // Purchase UoM
            
            // Odoo Inventory & Traceability Tab
            $table->boolean('track_inventory')->default(true)->after('type'); // Track Inventory Checkbox
            $table->enum('tracking', ['quantity', 'lot', 'serial'])->default('quantity')->after('track_inventory'); // By Quantity / By Lots / By Unique Serial Number
            $table->boolean('allow_negative_stock')->default(false)->after('tracking'); // Allow Negative Stock Checkbox
            $table->boolean('route_buy')->default(true)->after('allow_negative_stock');
            $table->boolean('route_manufacture')->default(false)->after('route_buy');
            $table->boolean('route_subcontract')->default(false)->after('route_manufacture');
            
            // Reordering Rules
            $table->decimal('max_stock', 12, 3)->default(0)->after('min_stock');
            $table->decimal('reorder_qty', 12, 3)->default(0)->after('max_stock');
            
            // Logistics & Lead Times
            $table->decimal('weight', 10, 3)->nullable()->after('reorder_qty'); // Net weight (kg)
            $table->decimal('volume', 10, 4)->nullable()->after('weight'); // m3
            $table->unsignedInteger('lead_time_days')->default(0)->after('volume'); // Customer / Delivery Lead Time
            $table->unsignedInteger('purchase_lead_time_days')->default(0)->after('lead_time_days'); // Vendor Lead Time
            
            // Purchase & Vendor Info Tab
            $table->string('preferred_vendor')->nullable()->after('purchase_lead_time_days');
            $table->string('vendor_code', 50)->nullable()->after('preferred_vendor');
            $table->text('purchase_description')->nullable()->after('description');
            
            // Accounting & Costing Tab
            $table->string('costing_method', 20)->default('average')->after('purchase_description'); // standard, average, fifo
            $table->string('valuation_method', 20)->default('automated')->after('costing_method'); // manual, automated
            $table->text('internal_notes')->nullable()->after('valuation_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'image',
                'barcode',
                'can_be_sold',
                'can_be_purchased',
                'can_be_manufactured',
                'can_be_subcontracted',
                'sale_price',
                'invoicing_policy',
                'uom_po',
                'track_inventory',
                'tracking',
                'allow_negative_stock',
                'route_buy',
                'route_manufacture',
                'route_subcontract',
                'max_stock',
                'reorder_qty',
                'weight',
                'volume',
                'lead_time_days',
                'purchase_lead_time_days',
                'preferred_vendor',
                'vendor_code',
                'purchase_description',
                'costing_method',
                'valuation_method',
                'internal_notes',
            ]);
        });
    }
};
