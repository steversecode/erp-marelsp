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
        Schema::create('uom_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('uom_categories')->onDelete('cascade');
            $table->string('name');
            $table->enum('uom_type', ['reference', 'smaller', 'bigger'])->default('reference');
            $table->decimal('ratio', 16, 6)->default(1.0);
            $table->decimal('rounding', 16, 6)->default(0.001);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uoms');
        Schema::dropIfExists('uom_categories');
    }
};
