<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'total_goods')) {
                $table->decimal('total_goods', 15, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('quotations', 'installation_total')) {
                $table->decimal('installation_total', 15, 2)->default(0)->after('total_goods');
            }
            if (!Schema::hasColumn('quotations', 'total_price')) {
                $table->decimal('total_price', 15, 2)->default(0)->after('installation_total');
            }
            if (!Schema::hasColumn('quotations', 'vat_total')) {
                $table->decimal('vat_total', 15, 2)->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('quotations', 'final_price')) {
                $table->decimal('final_price', 15, 2)->default(0)->after('vat_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['total_goods', 'installation_total', 'total_price', 'vat_total', 'final_price']);
        });
    }
};
