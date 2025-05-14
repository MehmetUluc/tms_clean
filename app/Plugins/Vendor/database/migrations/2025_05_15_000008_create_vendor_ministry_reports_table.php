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
        Schema::create('vendor_ministry_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->date('report_date');
            $table->enum('report_type', ['daily', 'monthly', 'quarterly', 'yearly', 'other'])->default('monthly');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('report_data')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users');
            $table->string('submission_reference')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes
            $table->index(['vendor_id', 'report_date']);
            $table->index(['hotel_id', 'report_date']);
            $table->index(['report_date', 'status']);
            $table->index(['report_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_ministry_reports');
    }
};