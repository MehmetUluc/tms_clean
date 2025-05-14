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
        Schema::create('discount_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('discount_id')->constrained('discounts');
            $table->foreignId('discount_code_id')->nullable()->constrained('discount_codes');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('order_type'); // For polymorphic relationship
            $table->unsignedBigInteger('order_id');
            $table->decimal('amount', 10, 2)->default(0); // The amount discounted
            $table->json('metadata')->nullable(); // Additional data about the discount usage
            $table->timestamps();
            $table->softDeletes();

            $table->index('discount_id');
            $table->index('discount_code_id');
            $table->index('user_id');
            $table->index(['order_type', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_usages');
    }
};