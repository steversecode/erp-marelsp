<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_moves', function (Blueprint $table) {
            $table->id();
            $table->string('move_number', 50)->unique(); // e.g. SM-2026-03-0001
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('from_location_id')->constrained('stock_locations')->cascadeOnDelete();
            $table->foreignId('to_location_id')->constrained('stock_locations')->cascadeOnDelete();
            $table->decimal('qty', 12, 4);
            $table->string('uom', 20)->default('kg');

            // Movement classification
            $table->enum('category', [
                'po_receipt',         // Stock In dari Supplier PO
                'production_return',  // Stock In Sisa Produksi dari MO
                'mo_consumption',     // Stock Out untuk Produksi MO
                'mo_finished_goods',  // Stock In Produk Jadi hasil MO
                'sample_issue',       // Stock Out untuk Produksi Sample / R&D
                'dyeing_out',         // Stock Out pengiriman ke proses celup
                'dyeing_return',      // Stock In hasil celup
                'adjustment'          // Penyesuaian / Stock Opname
            ]);

            // Polymorphic / Generic Reference Link
            $table->string('reference_type')->nullable(); // 'PurchaseOrder', 'ManufacturingOrder', etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable(); // e.g. PO-001, MO-001, SMPL-001, DYE-001

            $table->string('batch_lot_number')->nullable(); // Lot/Batch number benang/kain
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes for lightning fast stock ledger queries
            $table->index(['product_id', 'from_location_id']);
            $table->index(['product_id', 'to_location_id']);
            $table->index(['category', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_moves');
    }
};
