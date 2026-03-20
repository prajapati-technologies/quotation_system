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
        Schema::table('colors', function (Blueprint $table) {
            $table->foreignId('material_type_id')->nullable()->after('id')->constrained('material_types')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->after('material_type_id')->constrained('colors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colors', function (Blueprint $table) {
            $table->dropForeign(['material_type_id']);
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['material_type_id', 'parent_id']);
        });
    }
};
