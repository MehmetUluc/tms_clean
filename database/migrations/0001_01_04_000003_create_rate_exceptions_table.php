<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_period_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('base_price', 10, 2)->nullable();
            $table->json('prices')->nullable()->comment('Kişi sayısına göre fiyatlar');
            $table->integer('min_stay')->nullable();
            $table->integer('quantity')->nullable();
            $table->enum('sales_type', ['direct_sale', 'ask_availability', 'inquire_only'])->nullable();
            $table->boolean('status')->default(true);
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['rate_period_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_exceptions');
    }
};