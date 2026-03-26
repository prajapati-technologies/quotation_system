<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quotation_milestones')) {
            Schema::create('quotation_milestones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
                $table->string('label');
                $table->decimal('percentage', 5, 2);
                $table->decimal('amount', 12, 2);
                $table->string('status')->default('Pending'); // Pending, Paid, Approved, Rejected
                $table->string('receipt_path')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_milestones');
    }
};
