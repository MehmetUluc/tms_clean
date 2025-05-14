<?php

// Connect to database
$db = new PDO("mysql:host=localhost;dbname=filament", "root", "");

try {
    // Drop the constraint
    echo "Removing problematic constraint...\n";
    $db->exec("ALTER TABLE occupancy_rates DROP INDEX unique_default_occupancy_rate;");
    
    // Clean the data
    echo "Cleaning occupancy_rates table...\n";
    $db->exec("TRUNCATE TABLE occupancy_rates;");
    
    echo "Fixed successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}