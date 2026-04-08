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
        Schema::table('quotations', function (Blueprint $column) {
            $column->decimal('delivery_distance', 10, 2)->default(0)->after('vat_total');
            $column->decimal('delivery_charge', 10, 2)->default(0)->after('delivery_distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $column) {
            $column->dropColumn(['delivery_distance', 'delivery_charge']);
        });
    }
};
