<?php

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Varsa mevcut kullanıcıyı sil
DB::table('users')->where('id', 1)->delete();

// ID=1 olan admin kullanıcısını ekle
DB::table('users')->insert([
    'id' => 1,
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'created_at' => now(),
    'updated_at' => now(),
]);

// Rol tabloları varsa, admin rolü oluştur ve kullanıcıya ata
try {
    // Admin rolü oluştur
    $roleId = DB::table('roles')->insertGetId([
        'name' => 'super_admin',
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    // Role atama
    DB::table('model_has_roles')->insert([
        'role_id' => $roleId,
        'model_type' => 'App\\Models\\User',
        'model_id' => 1,
    ]);
    
    echo "Admin rolü oluşturuldu ve atandı.\n";
} catch (\Exception $e) {
    echo "Rol tabloları bulunamadı veya rol ataması yapılamadı: " . $e->getMessage() . "\n";
}

echo "Admin kullanıcısı oluşturuldu (ID: 1, E-posta: admin@example.com, Şifre: password)\n";