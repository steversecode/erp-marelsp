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
                $table->string('type', 50)->default('goods')->change();
            });
        } else {
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'goods'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->string('type', 50)->default('raw_material')->change();
            });
        } else {
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `type` ENUM('raw_material', 'work_in_progress', 'finished_good', 'sample') DEFAULT 'raw_material'");
        }
    }
};
