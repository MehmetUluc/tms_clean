<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ConsolidateMigrations extends Command
{
    protected $signature = 'migration:consolidate 
                            {--target= : Target directory to store consolidated migrations (default: database/consolidated)} 
                            {--dry-run : Only show what would be done without making changes}';
                            
    protected $description = 'Consolidate migrations from core and plugins into a clean, ordered set';

    // Migration groups in execution order
    protected $migrationGroups = [
        'structure' => [
            'users', 'regions', 'permissions', 'hotel_types', 'hotels', 
            'hotel_tags', 'hotel_amenities', 'hotel_contacts'
        ],
        'room_structure' => [
            'board_types', 'room_types', 'room_amenities', 'rooms'
        ],
        'reservation' => [
            'reservations', 'guests'
        ],
        'pricing' => [
            'rate_plans', 'daily_rates', 'occupancy_rates', 'child_policies', 
            'inventories', 'rate_periods', 'rate_exceptions', 'booking_prices'
        ],
        'integration' => [
            'channels', 'xml_mappings', 'api_users', 'api_mappings'
        ],
        'theme' => [
            'theme_settings'
        ],
        'session' => [
            'sessions'
        ]
    ];

    public function handle()
    {
        $this->info('Analyzing migrations for consolidation...');
        
        $targetDir = $this->option('target') ?: database_path('consolidated');
        $dryRun = $this->option('dry-run');
        
        if (!$dryRun && !File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }
        
        // Get all migrations from core and plugins
        $allMigrations = $this->getAllMigrations();
        $this->info('Found ' . count($allMigrations) . ' migrations');
        
        // Group migrations by table
        $tableGroups = $this->groupMigrationsByTable($allMigrations);
        
        // Check already run migrations to prevent issues
        $executedMigrations = $this->getExecutedMigrations();
        
        // Create consolidated migrations in the proper order
        $timestamp = date('Y_m_d');
        $batch = 1000; // Start with a high number for ordering
        
        foreach ($this->migrationGroups as $group => $tables) {
            $this->info("\nConsolidating group: {$group}");
            
            foreach ($tables as $tablePrefix) {
                $matchedTables = array_filter(array_keys($tableGroups), function ($table) use ($tablePrefix) {
                    return Str::startsWith($table, $tablePrefix) || $table === $tablePrefix;
                });
                
                foreach ($matchedTables as $table) {
                    if (!isset($tableGroups[$table])) {
                        continue;
                    }
                    
                    $this->consolidateTableMigrations(
                        $table, 
                        $tableGroups[$table], 
                        $targetDir, 
                        $timestamp, 
                        $batch++, 
                        $executedMigrations,
                        $dryRun
                    );
                }
            }
        }
        
        // Handle any remaining tables not in the groups
        $handledTables = [];
        foreach ($this->migrationGroups as $tables) {
            foreach ($tables as $tablePrefix) {
                $matchedTables = array_filter(array_keys($tableGroups), function ($table) use ($tablePrefix) {
                    return Str::startsWith($table, $tablePrefix) || $table === $tablePrefix;
                });
                $handledTables = array_merge($handledTables, $matchedTables);
            }
        }
        
        $remainingTables = array_diff(array_keys($tableGroups), $handledTables);
        
        if (!empty($remainingTables)) {
            $this->info("\nConsolidating remaining tables:");
            
            foreach ($remainingTables as $table) {
                $this->consolidateTableMigrations(
                    $table, 
                    $tableGroups[$table], 
                    $targetDir, 
                    $timestamp, 
                    $batch++, 
                    $executedMigrations,
                    $dryRun
                );
            }
        }
        
        if (!$dryRun) {
            $this->info("\nCreating migration registry file...");
            $this->createMigrationRegistryFile($targetDir);
        }
        
        $this->info("\nMigration consolidation " . ($dryRun ? "analysis" : "completed") . "!");
        
        if ($dryRun) {
            $this->info('Use without --dry-run to create consolidated migrations');
        } else {
            $this->info("Consolidated migrations are in: {$targetDir}");
            $this->info("To use them, run: php artisan migrate --path=" . str_replace(base_path() . '/', '', $targetDir));
        }
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
        $pluginsDir = app_path('Plugins');
        
        if (File::isDirectory($pluginsDir)) {
            $plugins = File::directories($pluginsDir);
            
            foreach ($plugins as $pluginDir) {
                $pluginName = basename($pluginDir);
                $migrationsPath = "{$pluginDir}/database/migrations";
                
                if (File::isDirectory($migrationsPath)) {
                    $pluginMigrations = File::glob("{$migrationsPath}/*.php");
                    foreach ($pluginMigrations as $migrationPath) {
                        $allMigrations[] = ['path' => $migrationPath, 'namespace' => $pluginName];
                    }
                }
            }
        }
        
        return $allMigrations;
    }
    
    protected function groupMigrationsByTable($migrations)
    {
        $tableGroups = [];
        
        foreach ($migrations as $migration) {
            $table = $this->getTargetTable($migration['path']);
            
            if ($table) {
                if (!isset($tableGroups[$table])) {
                    $tableGroups[$table] = [];
                }
                
                $tableGroups[$table][] = $migration;
            }
        }
        
        // Sort each group by timestamp
        foreach ($tableGroups as $table => $migrations) {
            usort($tableGroups[$table], function ($a, $b) {
                return $this->getMigrationTimestamp($a['path']) <=> $this->getMigrationTimestamp($b['path']);
            });
        }
        
        return $tableGroups;
    }
    
    protected function getTargetTable($path)
    {
        $content = file_get_contents($path);
        
        // Try to find Schema::create or Schema::table calls
        if (preg_match('/Schema::(create|table)\([\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            return $matches[2];
        }
        
        // Check filename as a fallback
        $filename = basename($path);
        if (preg_match('/_(create|update|add\w+_to)_([a-z0-9_]+)(?:_table)?\.php$/', $filename, $matches)) {
            return $matches[2];
        }
        
        return null;
    }
    
    protected function consolidateTableMigrations($table, $migrations, $targetDir, $timestamp, $batch, $executedMigrations, $dryRun)
    {
        $this->line(" - Consolidating migrations for table: {$table}");
        
        // Find the primary migration that creates the table
        $createMigration = null;
        $updateMigrations = [];
        
        foreach ($migrations as $migration) {
            $content = file_get_contents($migration['path']);
            
            if (Str::contains($content, "Schema::create('{$table}'") || 
                Str::contains($content, "Schema::create(\"{$table}\"")) {
                $createMigration = $migration;
            } else {
                $updateMigrations[] = $migration;
            }
            
            $shortPath = str_replace(base_path() . '/', '', $migration['path']);
            $filename = basename($migration['path']);
            
            // Check if this migration has been executed
            if (in_array($filename, $executedMigrations)) {
                $this->info("   - Migration already executed: {$shortPath}");
            }
        }
        
        // Determine the output file
        $outputTimestamp = sprintf("%s_%04d", $timestamp, $batch);
        $outputFilename = "{$outputTimestamp}_create_{$table}_table.php";
        $outputPath = "{$targetDir}/{$outputFilename}";
        
        // Generate the consolidated migration
        if ($createMigration) {
            $createContent = file_get_contents($createMigration['path']);
            $upMethod = $this->extractMethod($createContent, 'up');
            $downMethod = $this->extractMethod($createContent, 'down');
            
            // Merge updates into the up method if there are any
            if (!empty($updateMigrations)) {
                $this->line("   - Merging " . count($updateMigrations) . " update migrations");
                
                $columnsToAdd = [];
                
                foreach ($updateMigrations as $migration) {
                    $updateContent = file_get_contents($migration['path']);
                    $updateMethod = $this->extractMethod($updateContent, 'up');
                    
                    // Extract table modifications
                    if (preg_match_all('/\$table->([^;]+);/', $updateMethod, $matches)) {
                        foreach ($matches[0] as $columnDefinition) {
                            $columnsToAdd[] = trim($columnDefinition);
                        }
                    }
                }
                
                // Insert the columns into the create method
                if (!empty($columnsToAdd)) {
                    $mergedColumns = implode("\n            ", $columnsToAdd);
                    
                    // Find the position to insert before the closing of the create block
                    $upMethod = preg_replace(
                        '/(Schema::create\([\'"]' . $table . '[\'"],\s*function\s*\(Blueprint\s*\$table\)\s*{)(.*?)(}\);)/s',
                        "$1$2\n            // Merged from update migrations\n            {$mergedColumns}\n        $3",
                        $upMethod
                    );
                }
            }
            
            $migrationContent = $this->generateMigrationContent($table, $upMethod, $downMethod);
            
            if ($dryRun) {
                $this->line("   - Would create: {$outputFilename}");
            } else {
                File::put($outputPath, $migrationContent);
                $this->line("   - Created: {$outputFilename}");
            }
        } else {
            $this->warn("   - No 'create' migration found for {$table}");
        }
    }
    
    protected function extractMethod($content, $methodName)
    {
        if (preg_match('/public function ' . $methodName . '\(\): void\s*{(.*?)}/s', $content, $matches)) {
            return trim($matches[1]);
        }
        
        return '';
    }
    
    protected function generateMigrationContent($table, $upMethod, $downMethod)
    {
        return <<<PHP
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
{$upMethod}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
{$downMethod}
    }
};
PHP;
    }
    
    protected function getMigrationTimestamp($path)
    {
        $filename = basename($path);
        if (preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_/', $filename, $matches)) {
            return $matches[1];
        }
        return '0000_00_00_000000';
    }
    
    protected function getExecutedMigrations()
    {
        $executed = [];
        
        if (Schema::hasTable('migrations')) {
            $executed = DB::table('migrations')->pluck('migration')->toArray();
        }
        
        return $executed;
    }
    
    protected function createMigrationRegistryFile($targetDir)
    {
        $registryContent = "<?php\n\n";
        $registryContent .= "/**\n";
        $registryContent .= " * Migration Registry\n";
        $registryContent .= " * This file documents the consolidated migrations\n";
        $registryContent .= " * Generated on: " . date('Y-m-d H:i:s') . "\n";
        $registryContent .= " */\n\n";
        
        $registryContent .= "return [\n";
        
        $migrations = File::glob("{$targetDir}/*.php");
        usort($migrations, function ($a, $b) {
            return basename($a) <=> basename($b);
        });
        
        foreach ($migrations as $migration) {
            $filename = basename($migration);
            $table = $this->getTargetTable($migration);
            $registryContent .= "    '{$filename}' => '{$table}',\n";
        }
        
        $registryContent .= "];\n";
        
        File::put("{$targetDir}/registry.php", $registryContent);
    }
}