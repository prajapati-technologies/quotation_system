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
            $table->foreignId('glass_id')->nullable()->change();
            $table->foreignId('color_id')->nullable()->change();
            $table->string('product_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreignId('glass_id')->nullable(false)->change();
            $table->foreignId('color_id')->nullable(false)->change();
            // product_type was originally not nullable in create_quotation_items_table, but we can leave it nullable or revert if strictness is needed.
            // keeping it nullable is safer for now.
        });
    }
};
