<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('partial_payment_percent', 5, 2)->nullable()->after('final_price');
            $table->decimal('partial_payment_amount', 10, 2)->nullable()->after('partial_payment_percent');
            $table->timestamp('partial_payment_at')->nullable()->after('partial_payment_amount');
            $table->timestamp('full_payment_at')->nullable()->after('partial_payment_at');
            $table->decimal('full_payment_balance_amount', 10, 2)->nullable()->after('full_payment_at');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'partial_payment_percent',
                'partial_payment_amount',
                'partial_payment_at',
                'full_payment_at',
                'full_payment_balance_amount',
            ]);
        });
    }
};
