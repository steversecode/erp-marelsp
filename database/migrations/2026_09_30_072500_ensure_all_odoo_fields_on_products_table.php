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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image', 255)->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'invoicing_policy')) {
                $table->string('invoicing_policy', 30)->default('ordered')->after('sale_price');
            }
            if (!Schema::hasColumn('products', 'track_inventory')) {
                $table->boolean('track_inventory')->default(true)->after('type');
            }
            if (!Schema::hasColumn('products', 'allow_negative_stock')) {
                $table->boolean('allow_negative_stock')->default(false)->after('tracking');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('products', 'image')) $columnsToDrop[] = 'image';
            if (Schema::hasColumn('products', 'invoicing_policy')) $columnsToDrop[] = 'invoicing_policy';
            if (Schema::hasColumn('products', 'track_inventory')) $columnsToDrop[] = 'track_inventory';
            if (Schema::hasColumn('products', 'allow_negative_stock')) $columnsToDrop[] = 'allow_negative_stock';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
