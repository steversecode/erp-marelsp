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
        Schema::table('products_products', function (Blueprint $table) {
            if (! Schema::hasColumn('products_products', 'allow_negative_stock')) {
                $table->boolean('allow_negative_stock')->default(false)->after('is_storable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_products', function (Blueprint $table) {
            if (Schema::hasColumn('products_products', 'allow_negative_stock')) {
                $table->dropColumn('allow_negative_stock');
            }
        });
    }
};
