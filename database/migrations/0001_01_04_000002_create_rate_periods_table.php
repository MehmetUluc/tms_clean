<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('base_price', 10, 2);
            $table->string('currency')->default('TRY');
            $table->integer('min_stay')->default(1);
            $table->integer('max_stay')->nullable();
            $table->integer('quantity')->default(1)->comment('Available units/rooms');
            $table->enum('sales_type', ['direct_sale', 'ask_availability', 'inquire_only'])->default('direct_sale');
            $table->boolean('status')->default(true);
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_periods');
    }
};