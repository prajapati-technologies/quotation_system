<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Route::get('/', function () {
    return view('welcome');
});

// TEMPORARY FIX ROUTE
Route::get('/fix-db', function () {
    try {
        // Fix Quotations Table
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

        // Fix Quotation Items Table
        Schema::table('quotation_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_items', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('quotation_items', 'installation_rate')) {
                $table->decimal('installation_rate', 15, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('quotation_items', 'sub_color_id')) {
                $table->foreignId('sub_color_id')->nullable()->after('color_id');
            }
        });

        return "Database Fixes Applied Successfully! You can now save your quotations.";
    } catch (\Exception $e) {
        return "Database Already Fixed or Error: " . $e->getMessage();
    }
});
