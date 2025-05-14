<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


class MakeFilamentPlugin extends Command
{
    protected $signature = 'make:filament-plugin';
    protected $description = 'Laravel Filament için tam sistem plugin oluşturur.';

    public function handle()
    {
        $pluginName = $this->ask('Plugin adı ne olsun? (StudlyCase)', 'BlogManager');
        $vendorName = $this->ask('Vendor adı ne olsun?', 'Acme');
        $crud = $this->confirm('CRUD oluşturulsun mu?', true);
        $modelName = null;

        if ($crud) {
            $modelName = $this->ask('Model adı ne olsun? (StudlyCase)', 'Post');
        }

        $pluginStudly = Str::studly($pluginName);
        $vendorStudly = Str::studly($vendorName);
        $pluginSlug = Str::kebab($pluginName);
        $pluginSnake = Str::snake($pluginName);
        $basePath = base_path("packages/{$vendorStudly}/{$pluginStudly}");

        if (File::exists($basePath)) {
            $this->error('Bu plugin zaten var.');
            return Command::FAILURE;
        }

        // Temel klasörler
        File::makeDirectory("{$basePath}/src", 0755, true);
        File::makeDirectory("{$basePath}/config", 0755, true);
        File::makeDirectory("{$basePath}/routes", 0755, true);
        File::makeDirectory("{$basePath}/resources/views", 0755, true);
        File::makeDirectory("{$basePath}/database/migrations", 0755, true);

        // Temel dosyalar
        $this->generateFromStub('serviceprovider.stub', "{$basePath}/src/{$pluginStudly}ServiceProvider.php", compact('vendorStudly', 'pluginStudly', 'pluginSlug', 'pluginSnake'));
        $this->generateFromStub('plugin.stub', "{$basePath}/src/{$pluginStudly}Plugin.php", compact('vendorStudly', 'pluginStudly', 'pluginSlug'));
        $this->generateFromStub('config.stub', "{$basePath}/config/{$pluginSnake}.php", compact('pluginStudly'));
        $this->generateFromStub('routes.stub', "{$basePath}/routes/web.php", []);
        $this->generateFromStub('composer.stub', "{$basePath}/composer.json", compact('vendorStudly', 'pluginStudly', 'pluginSlug', 'pluginSnake'));
        $this->generateFromStub('readme.stub', "{$basePath}/README.md", compact('pluginStudly'));

        if ($crud && $modelName) {
            $this->generateModel($basePath, $modelName, compact('vendorStudly', 'pluginStudly'));
            $this->generateMigration($basePath, $modelName);
            $this->generateResource($basePath, $modelName, compact('vendorStudly', 'pluginStudly', 'pluginSlug'));
        }

        $this->info('Plugin başarıyla oluşturuldu!');
        return Command::SUCCESS;
    }

    protected function generateFromStub($stubName, $targetPath, $replacements)
    {
        $stubPath = resource_path('stubs/system-plugin/' . $stubName);
        $content = File::get($stubPath);

        foreach ($replacements as $key => $value) {
            $content = str_replace('{{' . strtoupper($key) . '}}', $value, $content);
        }

        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $content);
    }

    protected function generateModel($basePath, $modelName, $data)
    {
        $modelPath = "{$basePath}/src/Models/{$modelName}.php";
        File::ensureDirectoryExists(dirname($modelPath));
        $this->generateFromStub('model.stub', $modelPath, array_merge($data, ['modelname' => $modelName]));
    }

    protected function generateMigration($basePath, $modelName)
    {
        $tableName = Str::plural(Str::snake($modelName));
        $timestamp = now()->format('Y_m_d_His');
        $migrationPath = "{$basePath}/database/migrations/{$timestamp}_create_{$tableName}_table.php";

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        File::put($migrationPath, $content);
    }

    protected function generateResource($basePath, $modelName, $data)
    {
        $resourcePath = "{$basePath}/src/Filament/Resources/{$modelName}Resource.php";
        $pagesPath = "{$basePath}/src/Filament/Resources/{$modelName}Resource/Pages";

        File::ensureDirectoryExists(dirname($resourcePath));
        File::ensureDirectoryExists($pagesPath);

        $this->generateFromStub('resource.stub', $resourcePath, array_merge($data, ['modelname' => $modelName]));
        $this->generateFromStub('list.stub', "{$pagesPath}/List{$modelName}s.php", array_merge($data, ['modelname' => $modelName]));
        $this->generateFromStub('create.stub', "{$pagesPath}/Create{$modelName}.php", array_merge($data, ['modelname' => $modelName]));
        $this->generateFromStub('edit.stub', "{$pagesPath}/Edit{$modelName}.php", array_merge($data, ['modelname' => $modelName]));
    }
}
