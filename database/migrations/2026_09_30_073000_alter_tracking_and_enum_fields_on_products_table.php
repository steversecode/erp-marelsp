<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->string('tracking', 50)->default('quantity')->change();
                $table->string('costing_method', 50)->default('average')->change();
                $table->string('valuation_method', 50)->default('automated')->change();
            });
        } else {
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `tracking` VARCHAR(50) NOT NULL DEFAULT 'quantity'");
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `costing_method` VARCHAR(50) NOT NULL DEFAULT 'average'");
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `valuation_method` VARCHAR(50) NOT NULL DEFAULT 'automated'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->string('tracking', 50)->default('quantity')->change();
            });
        } else {
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `tracking` VARCHAR(50) NOT NULL DEFAULT 'quantity'");
        }
    }
};
