<?php

// Load the Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== TMS Migration Elle Kurulum Aracı ===\n\n";

try {
    // Önce migrations tablosunu oluştur
    if (!Schema::hasTable('migrations')) {
        Schema::create('migrations', function (Blueprint $table) {
            $table->id();
            $table->string('migration');
            $table->integer('batch');
        });
        echo "Migrations tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Migrations tablosu zaten mevcut.\n";
    }
    
    // Sonra users tablosunu oluştur
    if (!Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Migrations tablosuna kaydet
        DB::table('migrations')->insert([
            'migration' => '0001_01_01_000000_create_users_table',
            'batch' => 1
        ]);
        
        echo "Users tablosu başarıyla oluşturuldu ve migration kaydedildi.\n";
    } else {
        echo "Users tablosu zaten mevcut.\n";
    }
    
    // Permission tabloları için filament shield veya spatie tablosunu kontrol et
    $tableNames = config('permission.table_names', [
        'permissions' => 'permissions',
        'roles' => 'roles',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ]);
    
    $columnNames = config('permission.column_names', [
        'model_morph_key' => 'model_id'
    ]);
    
    // Permissions tablosu
    if (!Schema::hasTable($tableNames['permissions'])) {
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->string('module')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });
        echo "Permissions tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Permissions tablosu zaten mevcut.\n";
    }

    // Roles tablosu
    if (!Schema::hasTable($tableNames['roles'])) {
        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });
        echo "Roles tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Roles tablosu zaten mevcut.\n";
    }

    // Model Has Permissions tablosu
    if (!Schema::hasTable($tableNames['model_has_permissions'])) {
        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type']);
            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type']);
        });
        echo "Model Has Permissions tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Model Has Permissions tablosu zaten mevcut.\n";
    }

    // Model Has Roles tablosu
    if (!Schema::hasTable($tableNames['model_has_roles'])) {
        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type']);
            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type']);
        });
        echo "Model Has Roles tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Model Has Roles tablosu zaten mevcut.\n";
    }

    // Role Has Permissions tablosu
    if (!Schema::hasTable($tableNames['role_has_permissions'])) {
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            $table->primary(['permission_id', 'role_id']);
        });
        echo "Role Has Permissions tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Role Has Permissions tablosu zaten mevcut.\n";
    }
    
    // Migrations tablosuna permission migration'ını kaydet
    DB::table('migrations')->insert([
        'migration' => '0001_01_01_000001_create_permission_tables',
        'batch' => 1
    ]);
    
    echo "\nPermission tabloları için migration kaydı başarıyla oluşturuldu.\n";
    
    // Sessions tablosu
    if (!Schema::hasTable('sessions')) {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        
        // Migrations tablosuna kaydet
        DB::table('migrations')->insert([
            'migration' => '0001_01_01_000002_create_sessions_table',
            'batch' => 1
        ]);
        
        echo "Sessions tablosu başarıyla oluşturuldu ve migration kaydedildi.\n";
    } else {
        echo "Sessions tablosu zaten mevcut.\n";
    }
    
    echo "\nManuel kurulum tamamlandı. Şimdi kalan migration'ları çalıştırabilirsiniz:\n";
    echo "php artisan migrate\n";
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}