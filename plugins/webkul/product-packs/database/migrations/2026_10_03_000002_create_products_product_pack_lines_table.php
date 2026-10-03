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
        Schema::create('products_product_pack_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_product_id')
                ->constrained('products_products')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->cascadeOnDelete();

            $table->decimal('quantity', 15, 4)->default(1.0);

            $table->unique(['parent_product_id', 'product_id'], 'prod_pack_line_unique');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_product_pack_lines');
    }
};
