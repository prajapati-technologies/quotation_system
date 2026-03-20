<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key first, then drop the columns
        Schema::table('products', function (Blueprint $table) {
            try {
                $table->dropForeign(['main_color_id']);
                $table->dropForeign(['material_type_id']);
            } catch (\Exception $e) {
                // Ignore if not present
            }
            $table->dropColumn(['main_color_id', 'price', 'installation_price', 'material_type_id']);
        });

        Schema::create('product_color_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('main_color_id')->constrained('colors')->onDelete('cascade');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('installation_price', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::dropIfExists('product_sub_colors');
        Schema::create('product_color_price_sub_color', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_color_price_id')->constrained('product_color_prices')->onDelete('cascade');
            $table->foreignId('color_id')->constrained('colors')->onDelete('cascade');
            $table->timestamps();
        });
        
        // Add material_type_id back to product as requested at the top level
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('material_type_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_color_price_sub_color');
        Schema::dropIfExists('product_color_prices');
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('main_color_id')->nullable()->constrained('colors')->onDelete('set null');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('installation_price', 15, 2)->default(0);
        });
    }
};
