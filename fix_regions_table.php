<?php

// Import the necessary classes
require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Check and add missing columns to the regions table
if (Schema::hasTable('regions')) {
    echo "Fixing regions table structure...\n";

    // Check if parent_id column exists, if not add it
    if (!Schema::hasColumn('regions', 'parent_id')) {
        Schema::table('regions', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('regions')->nullOnDelete();
        });
        echo "- Added parent_id column\n";
    }
    
    // Check if type column exists, if not add it
    if (!Schema::hasColumn('regions', 'type')) {
        Schema::table('regions', function (Blueprint $table) {
            $table->enum('type', ['country', 'region', 'city', 'district'])->after('name')->default('region');
            $table->index('type');
        });
        echo "- Added type column\n";
    }
    
    // Add other columns as needed
    $columnsToAdd = [
        'code' => function(Blueprint $table) {
            $table->string('code', 10)->nullable()->after('slug')->comment('Ülke veya bölge kodu');
        },
        'latitude' => function(Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('description');
        },
        'longitude' => function(Blueprint $table) {
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        },
        'timezone' => function(Blueprint $table) {
            $table->string('timezone', 50)->nullable()->after('longitude');
        },
        'sort_order' => function(Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('is_active');
            $table->index('sort_order');
        },
        'is_featured' => function(Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->index('is_featured');
        }
    ];
    
    foreach ($columnsToAdd as $column => $callback) {
        if (!Schema::hasColumn('regions', $column)) {
            Schema::table('regions', $callback);
            echo "- Added {$column} column\n";
        }
    }
    
    echo "Regions table structure fixed successfully!\n";
} else {
    echo "Regions table not found!\n";
}

echo "Done.\n";