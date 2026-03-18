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
        // 1. Create Classifications Table
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->string('name'); // ALUMET, MUANGTHONG, SMS SCHIMMER, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Create Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classification_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Sliding Window 2 Panels, etc.
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('has_installation')->default(true);
            $table->decimal('installation_price', 10, 2)->default(700);
            $table->timestamps();
        });

        // 3. Create Glass Films Table
        Schema::create('glass_films', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 2 Ply Black 30, etc.
            $table->text('description')->nullable();
            $table->decimal('price_per_sqm', 10, 2)->default(0);
            $table->timestamps();
        });

        // 4. Update Colors Table
        Schema::table('colors', function (Blueprint $table) {
            // Check if column exists before adding it to avoid errors if re-running partially
            if (!Schema::hasColumn('colors', 'classification_id')) {
                $table->foreignId('classification_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('colors', 'category')) {
                $table->string('category')->nullable(); // Normal, Sahara
            }
            if (!Schema::hasColumn('colors', 'sub_category')) {
                $table->string('sub_category')->nullable(); // Powder Coat
            }
            if (!Schema::hasColumn('colors', 'code')) {
                $table->string('code')->nullable(); // 8911, 8823
            }
            // renaming additional_price to price_adjustment if needed or just using it
            // keeping additional_price as is, assuming it serves the purpose.
        });

        // 5. Update Glasses Table
        Schema::table('glasses', function (Blueprint $table) {
            if (!Schema::hasColumn('glasses', 'code')) {
                $table->string('code')->nullable(); // GL-01
            }
            if (!Schema::hasColumn('glasses', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('glasses', 'features')) {
                $table->text('features')->nullable();
            }
            // price already exists
            if (!Schema::hasColumn('glasses', 'price_per_sqm')) {
                $table->renameColumn('price', 'price_per_sqm');
            }
            if (!Schema::hasColumn('glasses', 'thickness')) {
                $table->string('thickness')->nullable();
            }
            if (!Schema::hasColumn('glasses', 'max_size')) {
                $table->string('max_size')->nullable();
            }
        });

        // 6. Update Quotation Items Table
        Schema::table('quotation_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_items', 'classification_id')) {
                $table->foreignId('classification_id')->nullable()->constrained();
            }
            if (!Schema::hasColumn('quotation_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained();
            }
            if (!Schema::hasColumn('quotation_items', 'glass_film_id')) {
                $table->foreignId('glass_film_id')->nullable()->constrained();
            }
            if (!Schema::hasColumn('quotation_items', 'installation_cost')) {
                $table->decimal('installation_cost', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('quotation_items', 'glass_cost')) {
                $table->decimal('glass_cost', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('quotation_items', 'film_cost')) {
                $table->decimal('film_cost', 10, 2)->default(0);
            }
            // Make product_type nullable as we serve product_id now, or keep it for legacy?
            // Ideally we should drop it or make it nullable.
            $table->string('product_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropForeign(['classification_id']);
            $table->dropColumn('classification_id');
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->dropForeign(['glass_film_id']);
            $table->dropColumn('glass_film_id');
            $table->dropColumn(['installation_cost', 'glass_cost', 'film_cost']);
        });

        Schema::table('glasses', function (Blueprint $table) {
            $table->dropColumn(['code', 'description', 'features', 'thickness', 'max_size']);
            $table->renameColumn('price_per_sqm', 'price');
        });

        Schema::table('colors', function (Blueprint $table) {
            $table->dropForeign(['classification_id']);
            $table->dropColumn(['classification_id', 'category', 'sub_category', 'code']);
        });

        Schema::dropIfExists('glass_films');
        Schema::dropIfExists('products');
        Schema::dropIfExists('classifications');
    }
};
