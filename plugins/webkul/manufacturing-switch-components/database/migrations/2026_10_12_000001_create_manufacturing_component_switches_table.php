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
        Schema::create('manufacturing_component_switches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('manufacturing_orders')
                ->cascadeOnDelete();

            $table->foreignId('original_move_id')
                ->nullable()
                ->constrained('inventories_moves')
                ->nullOnDelete();

            $table->foreignId('original_product_id')
                ->constrained('products_products')
                ->cascadeOnDelete();

            $table->decimal('original_quantity', 15, 4)->default(0);

            $table->foreignId('original_uom_id')
                ->nullable()
                ->constrained('unit_of_measures')
                ->nullOnDelete();

            $table->foreignId('replacement_move_id')
                ->nullable()
                ->constrained('inventories_moves')
                ->nullOnDelete();

            $table->foreignId('replacement_product_id')
                ->constrained('products_products')
                ->cascadeOnDelete();

            $table->decimal('replacement_quantity', 15, 4)->default(0);

            $table->foreignId('replacement_uom_id')
                ->nullable()
                ->constrained('unit_of_measures')
                ->nullOnDelete();

            $table->text('reason')->nullable();

            $table->foreignId('switched_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_component_switches');
    }
};
