<?php

// Script to clean pricing data for fresh start
$db = new PDO("mysql:host=localhost;dbname=filament", "root", "");

try {
    // Disable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Delete data from pricing related tables
    $tables = [
        'inventories',
        'child_policies',
        'occupancy_rates',
        'daily_rates',
        'rate_plans'
    ];
    
    foreach ($tables as $table) {
        echo "Cleaning table: {$table}...\n";
        $db->exec("TRUNCATE TABLE {$table}");
    }
    
    // Enable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "All pricing data has been cleaned successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}