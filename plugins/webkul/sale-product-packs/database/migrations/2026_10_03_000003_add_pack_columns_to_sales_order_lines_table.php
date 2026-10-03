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
        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->foreignId('pack_parent_line_id')
                ->nullable()
                ->after('linked_sale_order_sale_id')
                ->constrained('sales_order_lines')
                ->cascadeOnDelete();

            $table->string('pack_type')->nullable()->after('pack_parent_line_id');
            $table->string('pack_component_price')->nullable()->after('pack_type');
            $table->integer('pack_depth')->default(0)->after('pack_component_price');
            $table->boolean('is_pack')->default(false)->after('pack_depth');
            $table->boolean('is_pack_component')->default(false)->after('is_pack');
            $table->boolean('pack_modifiable')->default(false)->after('is_pack_component');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->dropForeign(['pack_parent_line_id']);
            $table->dropColumn([
                'pack_parent_line_id',
                'pack_type',
                'pack_component_price',
                'pack_depth',
                'is_pack',
                'is_pack_component',
                'pack_modifiable',
            ]);
        });
    }
};
