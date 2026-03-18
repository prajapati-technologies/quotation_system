<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_items', 'brand_id')) {
                $table->foreignId('brand_id')->nullable()->constrained('brands')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('quotation_items', 'material_type_id')) {
                $table->foreignId('material_type_id')->nullable()->constrained('material_types')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('quotation_items', 'accessories')) {
                $table->json('accessories')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            if (Schema::hasColumn('quotation_items', 'brand_id')) {
                $table->dropForeign(['brand_id']);
                $table->dropColumn('brand_id');
            }
            if (Schema::hasColumn('quotation_items', 'material_type_id')) {
                $table->dropForeign(['material_type_id']);
                $table->dropColumn('material_type_id');
            }
            if (Schema::hasColumn('quotation_items', 'accessories')) {
                $table->dropColumn('accessories');
            }
        });
    }
};
