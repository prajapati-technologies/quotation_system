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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->string('product_type'); // Window / Door
            $table->decimal('width', 10, 2);
            $table->decimal('height', 10, 2);
            $table->integer('quantity');
            $table->foreignId('material_id')->constrained();
            $table->foreignId('glass_id')->constrained()->nullable();
            $table->foreignId('color_id')->constrained()->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
