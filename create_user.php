<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Kullanıcı var mı kontrol et
$user = User::where('email', 'admin@test.com')->first();

if ($user) {
    echo "Kullanıcı zaten var. Şifre güncelleniyor...\n";
    $user->password = Hash::make('Admin123!');
    $user->save();
} else {
    // Yeni kullanıcı oluştur
    $user = new User();
    $user->name = 'Admin User';
    $user->email = 'admin@test.com';
    $user->password = Hash::make('Admin123!');
    $user->save();
    echo "Yeni kullanıcı oluşturuldu.\n";
}

echo "Kullanıcı bilgileri:\n";
echo "Email: admin@test.com\n";
echo "Şifre: Admin123!\n";