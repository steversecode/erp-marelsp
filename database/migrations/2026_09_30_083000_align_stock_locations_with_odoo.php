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
        Schema::table('stock_locations', function (Blueprint $table) {
            $table->string('type', 50)->default('internal')->change();
            if (!Schema::hasColumn('stock_locations', 'is_return')) {
                $table->boolean('is_return')->default(false)->after('is_scrap');
            }
            if (!Schema::hasColumn('stock_locations', 'barcode')) {
                $table->string('barcode', 50)->nullable()->after('code');
            }
            if (!Schema::hasColumn('stock_locations', 'comment')) {
                $table->text('comment')->nullable()->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_locations', function (Blueprint $table) {
            $table->dropColumn(['is_return', 'barcode', 'comment']);
        });
    }
};
