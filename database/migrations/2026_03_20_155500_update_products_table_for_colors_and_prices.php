<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('material_type_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('main_color_id')->nullable()->constrained('colors')->onDelete('set null');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('installation_price', 15, 2)->default(0);
        });

        Schema::create('product_sub_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->constrained('colors')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sub_colors');
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('material_type_id');
            $table->dropConstrainedForeignId('main_color_id');
            $table->dropColumn(['price', 'installation_price']);
        });
    }
};
