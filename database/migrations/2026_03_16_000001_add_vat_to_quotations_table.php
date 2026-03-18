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
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'vat_percent')) {
                $table->decimal('vat_percent', 5, 2)->default(0)->after('discount');
            }
            if (!Schema::hasColumn('quotations', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0)->after('vat_percent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'vat_amount')) {
                $table->dropColumn('vat_amount');
            }
            if (Schema::hasColumn('quotations', 'vat_percent')) {
                $table->dropColumn('vat_percent');
            }
        });
    }
};

