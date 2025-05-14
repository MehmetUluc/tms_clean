<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vendor Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings for the Vendor module.
    |
    */

    // Default vendor status
    'default_status' => 'pending',

    // Default commission rate for new vendors (percentage)
    'default_commission_rate' => 10.00,

    // Minimum commission rate allowed (percentage)
    'min_commission_rate' => 1.00,

    // Maximum commission rate allowed (percentage)
    'max_commission_rate' => 50.00,

    // Vendor statuses
    'statuses' => [
        'pending' => 'Pending',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ],

    // Document types
    'document_types' => [
        'contract' => 'Contract',
        'tax_certificate' => 'Tax Certificate',
        'license' => 'Business License',
        'identity' => 'Identity Document',
        'bank_statement' => 'Bank Statement',
        'authorization_letter' => 'Authorization Letter',
        'other' => 'Other',
    ],

    // Transaction types
    'transaction_types' => [
        'booking' => 'Booking',
        'cancellation' => 'Cancellation',
        'modification' => 'Modification',
        'payment' => 'Payment',
        'refund' => 'Refund',
        'adjustment' => 'Adjustment',
        'other' => 'Other',
    ],

    // Payment methods
    'payment_methods' => [
        'bank_transfer' => 'Bank Transfer',
        'credit_card' => 'Credit Card',
        'paypal' => 'PayPal',
        'check' => 'Check',
        'cash' => 'Cash',
        'other' => 'Other',
    ],

    // Ministry report types
    'ministry_report_types' => [
        'daily' => 'Daily',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly', 
        'yearly' => 'Yearly',
        'other' => 'Other',
    ],

    // Pagination
    'pagination' => [
        'vendors' => 20,
        'hotels' => 15,
        'transactions' => 25,
        'payments' => 20,
        'documents' => 25,
        'ministry_reports' => 20,
    ],

    // Routes
    'routes' => [
        'vendor_prefix' => 'vendor',
        'admin_prefix' => 'admin/vendors',
        'api_prefix' => 'api/vendors',
    ],

    // Notifications
    'notifications' => [
        'enable_email' => true,
        'enable_sms' => false,
        'enable_dashboard' => true,
    ],

    // File uploads
    'uploads' => [
        'document_max_size' => 10240, // 10MB in KB
        'allowed_document_types' => [
            'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'xls', 'xlsx'
        ],
        'disk' => 'local',
    ],

    // Currency
    'default_currency' => 'TRY',
    
    // Time settings
    'date_format' => 'd.m.Y',
    'time_format' => 'H:i',
    'datetime_format' => 'd.m.Y H:i',
];