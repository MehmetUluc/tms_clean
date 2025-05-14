<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrationRegistry
{
    protected static $migrationMap = [];
    protected static $disabledMigrations = [];
    protected static $loaded = false;

    /**
     * Register migrations from a specific path, handling duplicates
     *
     * @param string $path
     * @param string $namespace
     * @return void
     */
    public static function registerPath(string $path, string $namespace = 'app'): void
    {
        if (!self::$loaded) {
            self::loadExistingMigrations();
        }

        if (!File::isDirectory($path)) {
            return;
        }

        $files = File::glob($path . '/*.php');
        
        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $tableName = self::extractTableName($file);
            
            // Skip if this migration has been explicitly disabled
            if (in_array($filename, self::$disabledMigrations)) {
                continue;
            }
            
            // Check for existing migrations for the same table
            if (isset(self::$migrationMap[$tableName])) {
                $existing = self::$migrationMap[$tableName];
                
                // If there's an older migration for the same table, disable it
                if (self::compareTimestamps($filename, $existing['filename']) > 0) {
                    self::$disabledMigrations[] = $existing['filename'];
                    self::$migrationMap[$tableName] = [
                        'filename' => $filename,
                        'path' => $file,
                        'namespace' => $namespace
                    ];
                } else {
                    // Current migration is older, so disable it
                    self::$disabledMigrations[] = $filename;
                }
            } else {
                // No conflict, register this migration
                self::$migrationMap[$tableName] = [
                    'filename' => $filename,
                    'path' => $file,
                    'namespace' => $namespace
                ];
            }
        }
    }

    /**
     * Load existing migrations from the database
     *
     * @return void
     */
    protected static function loadExistingMigrations(): void
    {
        // Only attempt to load if the migrations table exists
        if (\Schema::hasTable('migrations')) {
            $migrations = \DB::table('migrations')->pluck('migration')->toArray();
            
            // Mark these migrations as already processed
            foreach ($migrations as $migration) {
                $tableName = self::extractTableNameFromFilename($migration);
                if ($tableName) {
                    self::$migrationMap[$tableName] = [
                        'filename' => $migration,
                        'path' => null, // Already executed
                        'namespace' => 'executed'
                    ];
                }
            }
        }
        
        self::$loaded = true;
    }

    /**
     * Extract table name from a migration file
     *
     * @param string $file
     * @return string|null
     */
    protected static function extractTableName(string $file): ?string
    {
        $contents = file_get_contents($file);
        
        // Extract table name from create or table methods
        if (preg_match('/Schema::create\([\'"]([^\'"]+)[\'"]/', $contents, $matches) ||
            preg_match('/Schema::table\([\'"]([^\'"]+)[\'"]/', $contents, $matches)) {
            return $matches[1];
        }
        
        // Extract from filename as fallback
        return self::extractTableNameFromFilename(pathinfo($file, PATHINFO_FILENAME));
    }
    
    /**
     * Extract table name from a migration filename
     *
     * @param string $filename
     * @return string|null
     */
    protected static function extractTableNameFromFilename(string $filename): ?string
    {
        // Remove timestamp prefix
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $filename);
        
        // Look for common patterns like create_X_table or update_X_table
        if (preg_match('/^create_([a-z0-9_]+)_table$/', $name, $matches) ||
            preg_match('/^update_([a-z0-9_]+)(_table)?$/', $name, $matches) ||
            preg_match('/^add_\w+_to_([a-z0-9_]+)(_table)?$/', $name, $matches) ||
            preg_match('/^modify_([a-z0-9_]+)(_table)?$/', $name, $matches)) {
            return $matches[1];
        }
        
        return $name; // Fallback to the full name without timestamp
    }
    
    /**
     * Compare two migration timestamps
     *
     * @param string $migration1
     * @param string $migration2
     * @return int
     */
    protected static function compareTimestamps(string $migration1, string $migration2): int
    {
        $timestamp1 = substr($migration1, 0, 17); // Get YYYY_MM_DD_HHMMSS
        $timestamp2 = substr($migration2, 0, 17);
        
        return strcmp($timestamp1, $timestamp2);
    }
    
    /**
     * Get the list of disabled migrations
     *
     * @return array
     */
    public static function getDisabledMigrations(): array
    {
        return self::$disabledMigrations;
    }
    
    /**
     * Clear the registry
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$migrationMap = [];
        self::$disabledMigrations = [];
        self::$loaded = false;
    }
}