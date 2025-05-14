<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\MigrationRegistry;

class FixMigrationConflicts extends Command
{
    protected $signature = 'migration:fix-conflicts {--dry-run : Only show what would be fixed without making changes}';
    protected $description = 'Fix migration conflicts between core and plugin migrations';

    // Tables with known conflicts
    protected $conflictingTables = [
        'rate_plans',
        'regions',
        'theme_settings',
    ];

    public function handle()
    {
        $this->info('Analyzing migration conflicts...');
        
        // Initialize registry
        MigrationRegistry::clear();
        
        // Register core migrations
        MigrationRegistry::registerPath(database_path('migrations'), 'core');
        
        // Get all plugin migration paths
        $pluginPaths = $this->getPluginMigrationPaths();
        
        // Register all plugin migrations
        foreach ($pluginPaths as $namespace => $path) {
            MigrationRegistry::registerPath($path, $namespace);
        }
        
        // Get disabled migrations
        $disabledMigrations = MigrationRegistry::getDisabledMigrations();
        
        if (empty($disabledMigrations)) {
            $this->info('No migration conflicts detected!');
            return;
        }
        
        $this->info('Found ' . count($disabledMigrations) . ' conflicting migrations:');
        
        foreach ($disabledMigrations as $migration) {
            $this->line(" - {$migration}");
        }
        
        // If we're in dry-run mode, stop here
        if ($this->option('dry-run')) {
            $this->info('Dry run completed. Use without --dry-run to fix conflicts.');
            return;
        }
        
        // Fix the conflicts
        $this->fixMigrationConflicts($disabledMigrations);
        
        $this->info('Migration conflicts fixed!');
        
        // Regenerate migrations registry to update timestamps
        $this->fixTimestampConflicts();
    }
    
    protected function getPluginMigrationPaths()
    {
        $pluginPaths = [];
        $pluginsDir = app_path('Plugins');
        
        if (!File::isDirectory($pluginsDir)) {
            return $pluginPaths;
        }
        
        $plugins = File::directories($pluginsDir);
        
        foreach ($plugins as $pluginDir) {
            $pluginName = basename($pluginDir);
            $migrationsPath = "{$pluginDir}/database/migrations";
            
            if (File::isDirectory($migrationsPath)) {
                $pluginPaths[$pluginName] = $migrationsPath;
            }
        }
        
        return $pluginPaths;
    }
    
    protected function fixMigrationConflicts(array $disabledMigrations)
    {
        // Find all migrations referencing the conflicting tables
        $allMigrations = $this->getAllMigrations();
        
        // Identify migrations to disable
        $toDisable = [];
        
        foreach ($this->conflictingTables as $table) {
            // Find migrations for this table
            $tableMigrations = array_filter($allMigrations, function ($migration) use ($table) {
                // Check if migration is related to this table
                $content = file_get_contents($migration['path']);
                return Str::contains($content, "'{$table}'") || 
                       Str::contains($content, "\"{$table}\"") ||
                       Str::contains(basename($migration['path']), $table);
            });
            
            // If we have conflicts, fix them
            if (count($tableMigrations) > 1) {
                $this->handleTableConflicts($table, $tableMigrations);
            }
        }
        
        // Mark disabled migrations in the migrations table if they've already run
        if (Schema::hasTable('migrations')) {
            foreach ($disabledMigrations as $migration) {
                if (DB::table('migrations')->where('migration', $migration)->exists()) {
                    $this->info("Marking {$migration} as disabled in migrations table");
                    
                    // Add a comment to indicate it's been disabled
                    DB::table('migrations')
                        ->where('migration', $migration)
                        ->update(['batch' => 0]); // Batch 0 will prevent it from being rolled back
                }
            }
        }
    }
    
    protected function handleTableConflicts($table, $migrations)
    {
        $this->info("\nFixing conflicts for table: {$table}");
        
        // Sort migrations by timestamp
        usort($migrations, function ($a, $b) {
            return $this->getMigrationTimestamp($b['path']) <=> $this->getMigrationTimestamp($a['path']);
        });
        
        // Keep the newest migration for creating the table
        $latestCreateMigration = null;
        
        foreach ($migrations as $migration) {
            $content = file_get_contents($migration['path']);
            
            if (Str::contains($content, "Schema::create('{$table}'") || 
                Str::contains($content, "Schema::create(\"{$table}\"")) {
                $latestCreateMigration = $migration;
                break;
            }
        }
        
        if ($latestCreateMigration) {
            $this->line(" - Using {$this->getShortPath($latestCreateMigration['path'])} as the primary migration");
            
            // Analyze all other migrations to merge them into a consolidated update migration
            $updates = [];
            
            foreach ($migrations as $migration) {
                if ($migration['path'] === $latestCreateMigration['path']) {
                    continue;
                }
                
                $content = file_get_contents($migration['path']);
                
                // Extract table modifications
                if (preg_match_all('/Schema::table\([\'"]'.$table.'[\'"],\s*function\s*\(Blueprint\s*\$table\)\s*{(.*?)}\);/s', $content, $matches)) {
                    foreach ($matches[1] as $tableModification) {
                        $updates[] = trim($tableModification);
                    }
                }
                
                $this->line(" - Extracted updates from {$this->getShortPath($migration['path'])}");
            }
            
            // If we found updates, create a consolidated migration
            if (!empty($updates)) {
                $timestamp = date('Y_m_d_His', time() + 10);
                $migrationName = "{$timestamp}_consolidate_{$table}_migrations.php";
                $migrationPath = database_path("migrations/{$migrationName}");
                
                $this->createConsolidatedMigration($table, $updates, $migrationPath);
                $this->line(" - Created consolidated migration: {$migrationName}");
            }
        } else {
            $this->warn(" - No 'create' migration found for {$table}");
        }
    }
    
    protected function getMigrationTimestamp($path)
    {
        $filename = basename($path);
        if (preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_/', $filename, $matches)) {
            return $matches[1];
        }
        return '0000_00_00_000000';
    }
    
    protected function getAllMigrations()
    {
        $allMigrations = [];
        
        // Core migrations
        $coreMigrations = File::glob(database_path('migrations/*.php'));
        foreach ($coreMigrations as $path) {
            $allMigrations[] = ['path' => $path, 'namespace' => 'core'];
        }
        
        // Plugin migrations
        $pluginPaths = $this->getPluginMigrationPaths();
        foreach ($pluginPaths as $namespace => $path) {
            $pluginMigrations = File::glob("{$path}/*.php");
            foreach ($pluginMigrations as $migrationPath) {
                $allMigrations[] = ['path' => $migrationPath, 'namespace' => $namespace];
            }
        }
        
        return $allMigrations;
    }
    
    protected function createConsolidatedMigration($table, $updates, $path)
    {
        $updatesCode = implode("\n            ", $updates);
        
        $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidated migration for {$table} table
     */
    public function up(): void
    {
        Schema::table('{$table}', function (Blueprint \$table) {
            {$updatesCode}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversing a consolidated migration is complex
        // Please rollback manually if needed
    }
};
PHP;

        File::put($path, $migrationContent);
    }
    
    protected function getShortPath($path)
    {
        return str_replace(base_path() . '/', '', $path);
    }
    
    protected function fixTimestampConflicts()
    {
        $this->info("\nChecking for timestamp conflicts...");
        
        // Check ThemeManager migrations (known to have duplicate timestamps)
        $themeManagerPath = app_path('Plugins/ThemeManager/database/migrations');
        $this->fixPluginTimestampConflicts($themeManagerPath, 'ThemeManager');
        
        // Check other plugins for timestamp conflicts
        $pluginPaths = $this->getPluginMigrationPaths();
        foreach ($pluginPaths as $namespace => $path) {
            if ($namespace === 'ThemeManager') {
                continue; // Already handled
            }
            
            $this->fixPluginTimestampConflicts($path, $namespace);
        }
    }
    
    protected function fixPluginTimestampConflicts($path, $namespace)
    {
        if (!File::isDirectory($path)) {
            return;
        }
        
        $files = File::glob("{$path}/*.php");
        
        // Group migrations by timestamp
        $timestampGroups = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $timestamp = $this->getMigrationTimestamp($file);
            $timestampGroups[$timestamp][] = $file;
        }
        
        // Check for conflicts
        $conflicts = false;
        foreach ($timestampGroups as $timestamp => $files) {
            if (count($files) > 1) {
                $conflicts = true;
                $this->info("Found timestamp conflict in {$namespace}: {$timestamp}");
                
                // Fix conflicts by updating timestamps
                $this->fixTimestampGroup($timestamp, $files);
            }
        }
        
        if (!$conflicts) {
            $this->line("No timestamp conflicts found in {$namespace}");
        }
    }
    
    protected function fixTimestampGroup($timestamp, $files)
    {
        // Keep the first file with the original timestamp
        $originalFile = array_shift($files);
        $this->line(" - Keeping original timestamp for: " . basename($originalFile));
        
        // Update timestamps for the rest
        $i = 1;
        foreach ($files as $file) {
            $filename = basename($file);
            $baseTimestamp = substr($timestamp, 0, 11); // YYYY_MM_DD_
            $seconds = substr($timestamp, 11, 6);       // HHMMSS
            
            // Generate a new timestamp by adding seconds
            $newSeconds = str_pad((int)$seconds + $i, 6, '0', STR_PAD_LEFT);
            $newTimestamp = $baseTimestamp . $newSeconds;
            
            // Create new filename
            $newFilename = str_replace($timestamp, $newTimestamp, $filename);
            $newPath = dirname($file) . '/' . $newFilename;
            
            // Rename the file
            File::move($file, $newPath);
            $this->line(" - Updated timestamp for {$filename} -> {$newFilename}");
            
            $i++;
        }
    }
}