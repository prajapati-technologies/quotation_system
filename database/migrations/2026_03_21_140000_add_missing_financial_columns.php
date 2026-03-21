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
        Schema::table('quotation_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_items', 'installation_rate')) {
                $table->decimal('installation_rate', 10, 2)->default(0)->after('price');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'vat_percent')) {
                $table->decimal('vat_percent', 5, 2)->default(0)->after('vat_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            if (Schema::hasColumn('quotation_items', 'installation_rate')) {
                $table->dropColumn('installation_rate');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'vat_percent')) {
                $table->dropColumn('vat_percent');
            }
        });
    }
};
