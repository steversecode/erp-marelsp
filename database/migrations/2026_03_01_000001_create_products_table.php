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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->enum('type', ['raw_material', 'work_in_progress', 'finished_good', 'sample'])->default('raw_material');
            $table->string('category')->nullable(); // e.g. Benang, Kain, Aksesoris, Pewarna
            $table->string('uom', 20)->default('kg'); // kg, cone, meter, pcs, gram
            $table->decimal('min_stock', 12, 3)->default(0);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
