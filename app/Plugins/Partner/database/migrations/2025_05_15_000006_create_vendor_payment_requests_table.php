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
        Schema::create('vendor_payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained('vendor_bank_accounts')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('TRY');
            $table->timestamp('requested_date');
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('processed_date')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->string('reference_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes
            $table->index(['vendor_id', 'status']);
            $table->index(['requested_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payment_requests');
    }
};