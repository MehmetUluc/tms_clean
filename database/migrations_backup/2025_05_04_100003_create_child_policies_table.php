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
        Schema::dropIfExists('child_policies');
        Schema::create('child_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            $table->integer('min_age')->comment('Minimum age for this policy (e.g., 0 for infants)');
            $table->integer('max_age')->comment('Maximum age for this policy (e.g., 6 for young children)');
            $table->enum('policy_type', ['free', 'fixed_price', 'percentage'])->default('free');
            $table->decimal('amount', 10, 2)->default(0)->comment('Price or percentage value, depending on policy_type');
            $table->string('currency', 3)->nullable()->comment('Only used for fixed_price');
            $table->integer('max_children')->default(1)->comment('Maximum number of children allowed with this policy');
            $table->integer('child_number')->default(1)->comment('1 for first child, 2 for second child, etc.');
            $table->timestamps();
            
            // Ensure no overlapping age ranges for the same rate plan and child number
            $table->unique(['rate_plan_id', 'child_number', 'min_age', 'max_age'], 'unique_child_policy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_policies');
    }
};