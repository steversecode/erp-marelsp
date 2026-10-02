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
        Schema::create('stock_locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // e.g. WH-MAIN, PROD-FLOOR, VENDOR-SUPPLIER, SUBCON-DYEING, VIRTUAL-SAMPLE, SCRAP
            $table->string('name', 100);
            $table->enum('type', ['internal', 'vendor', 'production', 'sample', 'dyeing_subcon', 'loss'])->default('internal');
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_locations');
    }
};
