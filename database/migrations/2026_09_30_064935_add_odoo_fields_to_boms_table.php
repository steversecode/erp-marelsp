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
        Schema::table('boms', function (Blueprint $table) {
            if (!Schema::hasColumn('boms', 'type')) {
                $table->string('type', 20)->default('normal')->after('name'); // 'normal' (Manufacture this product), 'phantom' (Kit)
            }
            if (!Schema::hasColumn('boms', 'ready_to_produce')) {
                $table->string('ready_to_produce', 30)->default('all_available')->after('notes');
            }
            if (!Schema::hasColumn('boms', 'consumption')) {
                $table->string('consumption', 30)->default('flexible')->after('ready_to_produce');
            }
            if (!Schema::hasColumn('boms', 'produce_delay')) {
                $table->integer('produce_delay')->default(0)->after('consumption');
            }
            if (!Schema::hasColumn('boms', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('boms', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('bom_items', function (Blueprint $table) {
            if (!Schema::hasColumn('bom_items', 'sequence')) {
                $table->integer('sequence')->default(0)->after('bom_id');
            }
            if (!Schema::hasColumn('bom_items', 'cones')) {
                $table->decimal('cones', 8, 2)->nullable()->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bom_items', function (Blueprint $table) {
            $table->dropColumn(['sequence', 'cones']);
        });

        Schema::table('boms', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['type', 'ready_to_produce', 'consumption', 'produce_delay', 'created_by', 'updated_by']);
        });
    }
};
