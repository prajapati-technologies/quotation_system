<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            // Drop current foreign key
            if (Schema::hasColumn('quotation_items', 'color_id')) {
                // MySQL usually names it like this: table_column_foreign
                try {
                    $table->dropForeign(['color_id']);
                } catch (\Exception $e) {
                    // Fallback to name if key doesn't match standard
                }
                
                // Add it back with onDelete('set null')
                $table->foreignId('color_id')->nullable()->change()->constrained('colors')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->foreignId('color_id')->nullable()->change()->constrained('colors');
        });
    }
};
