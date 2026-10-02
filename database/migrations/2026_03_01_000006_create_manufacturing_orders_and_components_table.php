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
        Schema::create('manufacturing_orders', function (Blueprint $table) {
            $table->id();
            $table->string('mo_number', 50)->unique(); // e.g. MO-2026-03-0001
            $table->foreignId('bom_id')->constrained('boms')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); // Target output
            $table->decimal('planned_qty', 12, 3);
            $table->decimal('produced_qty', 12, 3)->default(0);
            $table->string('uom', 20)->default('pcs');
            $table->enum('status', ['draft', 'confirmed', 'in_progress', 'done', 'cancelled'])->default('draft');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('source_location_id')->nullable()->constrained('stock_locations')->nullOnDelete(); // Gudang Bahan Baku
            $table->foreignId('destination_location_id')->nullable()->constrained('stock_locations')->nullOnDelete(); // Gudang Barang Jadi
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('mo_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturing_order_id')->constrained('manufacturing_orders')->cascadeOnDelete();
            
            // Component Switching architecture:
            $table->foreignId('original_product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('actual_product_id')->constrained('products')->cascadeOnDelete();
            $table->boolean('is_switched')->default(false);
            $table->text('switch_reason')->nullable();
            $table->foreignId('switched_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('switched_at')->nullable();

            // Quantities:
            $table->decimal('planned_qty', 12, 4); // Target required qty
            $table->decimal('issued_qty', 12, 4)->default(0); // Qty released/consumed into production floor
            $table->decimal('returned_qty', 12, 4)->default(0); // Qty leftover returned to warehouse
            $table->string('uom', 20)->default('kg');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mo_components');
        Schema::dropIfExists('manufacturing_orders');
    }
};
