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
            $table->boolean('is_pack')->default(false)->after('is_configurable');
            $table->string('pack_type')->default('detailed')->after('is_pack');
            $table->string('pack_component_price')->default('detailed')->after('pack_type');
            $table->boolean('pack_modifiable')->default(false)->after('pack_component_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_products', function (Blueprint $table) {
            $table->dropColumn([
                'is_pack',
                'pack_type',
                'pack_component_price',
                'pack_modifiable',
            ]);
        });
    }
};
