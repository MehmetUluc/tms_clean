<?php

namespace App\Services;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use App\Services\MigrationRegistry;

class FilteredMigrationRepository extends DatabaseMigrationRepository
{
    /**
     * Get the list of migrations for a batch.
     *
     * @param  int  $batch
     * @return array
     */
    public function getMigrationsByBatch($batch)
    {
        $migrations = parent::getMigrationsByBatch($batch);
        
        // Filter out any migrations that are in the disabled list
        $disabledMigrations = MigrationRegistry::getDisabledMigrations();
        
        return array_filter($migrations, function ($migration) use ($disabledMigrations) {
            return !in_array($migration->migration, $disabledMigrations);
        });
    }
    
    /**
     * Get the list of migration files.
     *
     * @return array
     */
    public function getMigrationFiles($files)
    {
        // Filter out any migrations that are in the disabled list
        $disabledMigrations = MigrationRegistry::getDisabledMigrations();
        
        return array_filter($files, function ($file) use ($disabledMigrations) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            return !in_array($filename, $disabledMigrations);
        });
    }
}