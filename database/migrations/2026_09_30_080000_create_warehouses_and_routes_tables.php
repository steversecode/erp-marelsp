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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('address')->nullable();
            
            // Primary Locations Linkage
            $table->foreignId('view_location_id')->nullable()->constrained('stock_locations')->nullOnDelete();
            $table->foreignId('lot_stock_id')->nullable()->constrained('stock_locations')->nullOnDelete();
            
            // Shipments & Operations Configuration (Odoo Standard)
            $table->string('incoming_steps', 20)->default('1_step'); // 1_step, 2_steps, 3_steps
            $table->string('outgoing_steps', 20)->default('1_step'); // 1_step, 2_steps, 3_steps
            
            // Resupply Rules & Manufacturing
            $table->boolean('buy_to_resupply')->default(true);
            $table->boolean('manufacture_to_resupply')->default(false);
            $table->string('manufacture_steps', 20)->default('2_steps'); // 1_step, 2_steps, 3_steps
            $table->json('resupply_from_warehouse_ids')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Add warehouse_id to stock_locations
        Schema::table('stock_locations', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('id')->constrained('warehouses')->nullOnDelete();
        });

        // Add route_mto & route_resupply_ids to products
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('route_mto')->default(false)->after('route_subcontract');
            $table->json('route_resupply_ids')->nullable()->after('route_mto');
            $table->foreignId('warehouse_id')->nullable()->after('route_resupply_ids')->constrained('warehouses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['route_mto', 'route_resupply_ids', 'warehouse_id']);
        });

        Schema::table('stock_locations', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['warehouse_id']);
        });

        Schema::dropIfExists('warehouses');
    }
};
