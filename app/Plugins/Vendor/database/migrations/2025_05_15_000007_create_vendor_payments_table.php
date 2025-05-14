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
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('payment_request_id')->nullable()->constrained('vendor_payment_requests')->onDelete('set null');
            $table->foreignId('bank_account_id')->nullable()->constrained('vendor_bank_accounts')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('TRY');
            $table->timestamp('payment_date');
            $table->timestamp('due_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'paypal', 'check', 'cash', 'other'])->default('bank_transfer');
            $table->string('payment_reference')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('receipt_file_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes
            $table->index(['vendor_id', 'payment_date']);
            $table->index(['vendor_id', 'status']);
            $table->index(['payment_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payments');
    }
};