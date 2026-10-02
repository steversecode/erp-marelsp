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
        Schema::create('product_substitutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('substitute_product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('conversion_rate', 8, 4)->default(1.0000); // 1 unit of primary = conversion_rate units of substitute
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['primary_product_id', 'substitute_product_id'], 'prod_substitute_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_substitutes');
    }
};
