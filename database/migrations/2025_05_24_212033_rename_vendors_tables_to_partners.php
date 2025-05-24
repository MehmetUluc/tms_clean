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
        // Rename main vendors table to partners
        Schema::rename('vendors', 'partners');
        
        // Rename related tables
        Schema::rename('vendor_bank_accounts', 'partner_bank_accounts');
        Schema::rename('vendor_commissions', 'partner_commissions');
        Schema::rename('vendor_documents', 'partner_documents');
        Schema::rename('vendor_ministry_reports', 'partner_ministry_reports');
        Schema::rename('vendor_payments', 'partner_payments');
        Schema::rename('vendor_payment_requests', 'partner_payment_requests');
        Schema::rename('vendor_transactions', 'partner_transactions');
        
        // Update foreign key column names
        Schema::table('partner_bank_accounts', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'partner_id');
        });
        
        Schema::table('partner_commissions', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'partner_id');
        });
        
        Schema::table('partner_documents', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'partner_id');
        });
        
        Schema::table('partner_ministry_reports', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'partner_id');
        });
        
        Schema::table('partner_payments', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'partner_id');
        });
        
        Schema::table('partner_payment_requests', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'partner_id');
        });
        
        Schema::table('partner_transactions', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'partner_id');
        });
        
        // Update hotels table
        Schema::table('hotels', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'partner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back to vendors
        Schema::rename('partners', 'vendors');
        
        // Rename related tables back
        Schema::rename('partner_bank_accounts', 'vendor_bank_accounts');
        Schema::rename('partner_commissions', 'vendor_commissions');
        Schema::rename('partner_documents', 'vendor_documents');
        Schema::rename('partner_ministry_reports', 'vendor_ministry_reports');
        Schema::rename('partner_payments', 'vendor_payments');
        Schema::rename('partner_payment_requests', 'vendor_payment_requests');
        Schema::rename('partner_transactions', 'vendor_transactions');
        
        // Update foreign key column names back
        Schema::table('vendor_bank_accounts', function (Blueprint $table) {
            $table->renameColumn('partner_id', 'vendor_id');
        });
        
        Schema::table('vendor_commissions', function (Blueprint $table) {
            $table->renameColumn('partner_id', 'vendor_id');
        });
        
        Schema::table('vendor_documents', function (Blueprint $table) {
            $table->renameColumn('partner_id', 'vendor_id');
        });
        
        Schema::table('vendor_ministry_reports', function (Blueprint $table) {
            $table->renameColumn('partner_id', 'vendor_id');
        });
        
        Schema::table('vendor_payments', function (Blueprint $table) {
            $table->renameColumn('partner_id', 'vendor_id');
        });
        
        Schema::table('vendor_payment_requests', function (Blueprint $table) {
            $table->renameColumn('partner_id', 'vendor_id');
        });
        
        Schema::table('vendor_transactions', function (Blueprint $table) {
            $table->renameColumn('partner_id', 'vendor_id');
        });
        
        // Update hotels table
        Schema::table('hotels', function (Blueprint $table) {
            $table->renameColumn('partner_id', 'vendor_id');
        });
    }
};
