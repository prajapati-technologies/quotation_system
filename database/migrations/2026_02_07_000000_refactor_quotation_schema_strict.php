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
        // 1. Refactor Materials
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        } else {
            Schema::table('materials', function (Blueprint $table) {
                if (Schema::hasColumn('materials', 'type'))
                    $table->dropColumn('type');
                if (Schema::hasColumn('materials', 'base_price'))
                    $table->dropColumn('base_price');
                // Ensure name column ? It should exist.
            });
        }

        // 2. Refactor MaterialTypes
        // MaterialType MUST be linked to Material.
        if (!Schema::hasTable('material_types')) {
            Schema::create('material_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('material_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->timestamps();
            });
        }

        // 3. Create Brands (RateSheet)
        // Replaces usage of 'classifications' for ALUMET, MUANGTHONG etc.
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->foreignId('material_id')->constrained()->cascadeOnDelete();
                $table->string('name'); // ALUMET, MUANGTHONG
                $table->timestamps();
            });
        }

        // 4. Refactor Products
        // Strict: NO PRICES here.
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'price'))
                    $table->dropColumn('price');
                if (Schema::hasColumn('products', 'installation_price'))
                    $table->dropColumn('installation_price');
                if (Schema::hasColumn('products', 'classification_id')) {
                    // Check if foreign key exists before dropping? 
                    // Simplest is to just drop column, migration might fail if specific constraint name not known.
                    // But let's try dropping the column.
                    $table->dropForeign(['classification_id']);
                    $table->dropColumn('classification_id');
                }
                if (Schema::hasColumn('products', 'has_installation'))
                    $table->dropColumn('has_installation');
            });
        } else {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        // 5. Create BrandRates
        // STRICT PRICE SOURCE
        if (!Schema::hasTable('brand_rates')) {
            Schema::create('brand_rates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->decimal('normal_price', 10, 2)->default(0);
                $table->decimal('special_price', 10, 2)->default(0);
                $table->decimal('installation_price', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        // 6. Refactor Colors
        // STRICT PRICE LOGIC
        if (Schema::hasTable('colors')) {
            Schema::table('colors', function (Blueprint $table) {
                if (!Schema::hasColumn('colors', 'color_type')) {
                    $table->string('color_type')->default('NORMAL'); // NORMAL, SPECIAL
                }
                if (!Schema::hasColumn('colors', 'additional_price')) {
                    $table->decimal('additional_price', 10, 2)->default(0);
                }
                if (!Schema::hasColumn('colors', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->constrained('categories');
                }
            });
        }

        // 7. Cleanup
        if (Schema::hasTable('colors')) {
            Schema::table('colors', function (Blueprint $table) {
                if (Schema::hasColumn('colors', 'classification_id')) {
                    $table->dropForeign(['classification_id']);
                    $table->dropColumn('classification_id');
                }
            });
        }
        if (Schema::hasTable('quotation_items')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                if (Schema::hasColumn('quotation_items', 'classification_id')) {
                    $table->dropForeign(['classification_id']);
                    $table->dropColumn('classification_id');
                }
            });
        }
        Schema::dropIfExists('classifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible in strict context without data loss usually, but standard down:
        Schema::dropIfExists('brand_rates');
        Schema::dropIfExists('brands');
    }
};
