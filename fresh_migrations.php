<?php

// Tüm migrasyonları temizleyip baştan oluşturmak için script

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

echo "=== TMS Temiz Migrasyon Oluşturma Aracı ===\n\n";

// Kullanıcıdan onay alalım
echo "DİKKAT: Bu işlem tüm veritabanını temizleyecek ve yeni bir şema oluşturacaktır.\n";
echo "Devam etmek istiyor musunuz? (evet/hayır): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
if (strtolower($line) !== 'evet') {
    echo "İşlem iptal edildi.\n";
    exit;
}

// 1. Veritabanıyla ilgili yorum - bu kısım atlanacak
echo "\nVeritabanı işlemleri atlanıyor - veritabanı bağlantısı mevcut değil.\n";

// 2. Migrasyon klasörlerini temizle ve yeni sıralı migrasyonlar oluştur
echo "\nTemiz migrasyonlar oluşturma işlemi başlatılıyor...\n";

// Temiz migrasyon dosyalarını saklayacağımız klasör
$exportDir = __DIR__ . '/database/migrations_clean';
if (!File::exists($exportDir)) {
    File::makeDirectory($exportDir, 0755, true);
}

// Mevcut migrasyonları temizle
echo "Temiz migrasyon klasörünü hazırlıyorum...\n";
File::cleanDirectory($exportDir);

// 3. Ana migrasyonları oluştur
echo "\nAna migrasyonları oluşturuyorum...\n";

// Kullanıcılar tablosu
$usersMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('email')->unique();
            \$table->timestamp('email_verified_at')->nullable();
            \$table->string('password');
            \$table->rememberToken();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
EOT;

File::put($exportDir . '/0001_01_01_000000_create_users_table.php', $usersMigration);
echo "- Users tablosu migrasyonu oluşturuldu.\n";

// İzinler tablosu
$permissionsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \$tableNames = config('permission.table_names');
        \$columnNames = config('permission.column_names');

        // Permissions tablosu
        Schema::create(\$tableNames['permissions'], function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('guard_name');
            \$table->string('module')->nullable();
            \$table->string('description')->nullable();
            \$table->timestamps();
            \$table->unique(['name', 'guard_name']);
        });

        // Roles tablosu
        Schema::create(\$tableNames['roles'], function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('guard_name');
            \$table->timestamps();
            \$table->unique(['name', 'guard_name']);
        });

        // Model Has Permissions tablosu
        Schema::create(\$tableNames['model_has_permissions'], function (Blueprint \$table) use (\$tableNames, \$columnNames) {
            \$table->unsignedBigInteger('permission_id');
            \$table->string('model_type');
            \$table->unsignedBigInteger(\$columnNames['model_morph_key']);
            \$table->index([\$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');
            \$table->foreign('permission_id')
                ->references('id')
                ->on(\$tableNames['permissions'])
                ->onDelete('cascade');
            \$table->primary(['permission_id', \$columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_permission_model_type_primary');
        });

        // Model Has Roles tablosu
        Schema::create(\$tableNames['model_has_roles'], function (Blueprint \$table) use (\$tableNames, \$columnNames) {
            \$table->unsignedBigInteger('role_id');
            \$table->string('model_type');
            \$table->unsignedBigInteger(\$columnNames['model_morph_key']);
            \$table->index([\$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');
            \$table->foreign('role_id')
                ->references('id')
                ->on(\$tableNames['roles'])
                ->onDelete('cascade');
            \$table->primary(['role_id', \$columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_role_model_type_primary');
        });

        // Role Has Permissions tablosu
        Schema::create(\$tableNames['role_has_permissions'], function (Blueprint \$table) use (\$tableNames) {
            \$table->unsignedBigInteger('permission_id');
            \$table->unsignedBigInteger('role_id');
            \$table->foreign('permission_id')
                ->references('id')
                ->on(\$tableNames['permissions'])
                ->onDelete('cascade');
            \$table->foreign('role_id')
                ->references('id')
                ->on(\$tableNames['roles'])
                ->onDelete('cascade');
            \$table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        \$tableNames = config('permission.table_names');
        Schema::dropIfExists(\$tableNames['role_has_permissions']);
        Schema::dropIfExists(\$tableNames['model_has_roles']);
        Schema::dropIfExists(\$tableNames['model_has_permissions']);
        Schema::dropIfExists(\$tableNames['roles']);
        Schema::dropIfExists(\$tableNames['permissions']);
    }
};
EOT;

File::put($exportDir . '/0001_01_01_000001_create_permission_tables.php', $permissionsMigration);
echo "- İzinler tablosu migrasyonu oluşturuldu.\n";

// Sessions tablosu
$sessionsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint \$table) {
            \$table->string('id')->primary();
            \$table->foreignId('user_id')->nullable()->index();
            \$table->string('ip_address', 45)->nullable();
            \$table->text('user_agent')->nullable();
            \$table->longText('payload');
            \$table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
EOT;

File::put($exportDir . '/0001_01_01_000002_create_sessions_table.php', $sessionsMigration);
echo "- Sessions tablosu migrasyonu oluşturuldu.\n";

// 4. Konaklama modülü migrasyonları
echo "\nKonaklama modülü migrasyonlarını oluşturuyorum...\n";

// Bölgeler tablosu
$regionsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('parent_id')->nullable()->constrained('regions')->nullOnDelete();
            \$table->string('name');
            \$table->enum('type', ['country', 'region', 'city', 'district'])->default('region');
            \$table->string('slug')->unique();
            \$table->string('code', 10)->nullable()->comment('Ülke veya bölge kodu');
            \$table->text('description')->nullable();
            \$table->decimal('latitude', 10, 7)->nullable();
            \$table->decimal('longitude', 10, 7)->nullable();
            \$table->string('timezone', 50)->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->boolean('is_featured')->default(false);
            \$table->integer('sort_order')->default(0);
            \$table->string('meta_title')->nullable();
            \$table->text('meta_description')->nullable();
            \$table->string('meta_keywords')->nullable();
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
            
            \$table->index('type');
            \$table->index('sort_order');
            \$table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000001_create_regions_table.php', $regionsMigration);
echo "- Regions tablosu migrasyonu oluşturuldu.\n";

// Otel tipleri tablosu
$hotelTypesMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_types', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->text('description')->nullable();
            \$table->string('icon')->nullable();
            \$table->integer('sort_order')->default(0);
            \$table->boolean('is_active')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_types');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000002_create_hotel_types_table.php', $hotelTypesMigration);
echo "- Hotel Types tablosu migrasyonu oluşturuldu.\n";

// Otel etiketleri tablosu
$hotelTagsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_tags', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->string('type')->nullable();
            \$table->string('icon')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->boolean('is_featured')->default(false);
            \$table->integer('sort_order')->default(0);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_tags');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000003_create_hotel_tags_table.php', $hotelTagsMigration);
echo "- Hotel Tags tablosu migrasyonu oluşturuldu.\n";

// Oteller tablosu
$hotelsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->foreignId('region_id')->constrained();
            \$table->foreignId('hotel_type_id')->nullable()->constrained()->nullOnDelete();
            \$table->text('description')->nullable();
            \$table->text('short_description')->nullable();
            \$table->string('address')->nullable();
            \$table->string('phone')->nullable();
            \$table->string('email')->nullable();
            \$table->string('website')->nullable();
            \$table->decimal('latitude', 10, 7)->nullable();
            \$table->decimal('longitude', 10, 7)->nullable();
            \$table->integer('star_rating')->nullable();
            \$table->decimal('avg_rating', 3, 2)->nullable();
            \$table->json('amenities')->nullable()->default('[]');
            \$table->json('policies')->nullable()->default('[]');
            \$table->json('gallery')->nullable()->default('[]');
            \$table->string('featured_image')->nullable();
            \$table->json('check_in_out')->nullable()->default('{"check_in_from": "14:00", "check_in_until": "23:59", "check_out_from": "07:00", "check_out_until": "12:00"}');
            \$table->boolean('is_active')->default(true);
            \$table->boolean('is_featured')->default(false);
            \$table->integer('sort_order')->default(0);
            \$table->string('meta_title')->nullable();
            \$table->text('meta_description')->nullable();
            \$table->string('meta_keywords')->nullable();
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
            
            \$table->index('is_active');
            \$table->index('is_featured');
            \$table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000004_create_hotels_table.php', $hotelsMigration);
echo "- Hotels tablosu migrasyonu oluşturuldu.\n";

// Pansiyon tipleri tablosu
$boardTypesMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_types', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('code', 10)->unique();
            \$table->text('description')->nullable();
            \$table->string('icon')->nullable();
            \$table->json('includes')->nullable();
            \$table->json('excludes')->nullable();
            \$table->integer('sort_order')->default(0);
            \$table->boolean('is_active')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_types');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000005_create_board_types_table.php', $boardTypesMigration);
echo "- Board Types tablosu migrasyonu oluşturuldu.\n";

// Hotel iletişim bilgileri tablosu
$hotelContactsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_contacts', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            \$table->string('name');
            \$table->string('position')->nullable();
            \$table->string('department')->nullable();
            \$table->string('email')->nullable();
            \$table->string('phone')->nullable();
            \$table->string('mobile')->nullable();
            \$table->boolean('is_primary')->default(false);
            \$table->boolean('is_active')->default(true);
            \$table->text('notes')->nullable();
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_contacts');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000006_create_hotel_contacts_table.php', $hotelContactsMigration);
echo "- Hotel Contacts tablosu migrasyonu oluşturuldu.\n";

// Otel özellikleri tablosu
$hotelAmenitiesMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_amenities', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->string('category')->nullable();
            \$table->string('icon')->nullable();
            \$table->text('description')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->integer('sort_order')->default(0);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
        
        Schema::create('hotel_hotel_amenity', function (Blueprint \$table) {
            \$table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            \$table->foreignId('hotel_amenity_id')->constrained()->onDelete('cascade');
            \$table->primary(['hotel_id', 'hotel_amenity_id']);
            \$table->timestamps();
        });
        
        Schema::create('hotel_hotel_tag', function (Blueprint \$table) {
            \$table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            \$table->foreignId('hotel_tag_id')->constrained()->onDelete('cascade');
            \$table->primary(['hotel_id', 'hotel_tag_id']);
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_hotel_tag');
        Schema::dropIfExists('hotel_hotel_amenity');
        Schema::dropIfExists('hotel_amenities');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000007_create_hotel_amenities_tables.php', $hotelAmenitiesMigration);
echo "- Hotel Amenities tablosu migrasyonu oluşturuldu.\n";

// Oda tipleri tablosu
$roomTypesMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('slug');
            \$table->text('description')->nullable();
            \$table->string('code')->nullable();
            \$table->integer('base_capacity')->default(2);
            \$table->integer('max_capacity')->default(2);
            \$table->integer('max_adults')->default(2);
            \$table->integer('max_children')->default(0);
            \$table->integer('max_infants')->default(0);
            \$table->string('size')->nullable();
            \$table->string('size_unit')->default('m²');
            \$table->json('features')->nullable();
            \$table->json('gallery')->nullable();
            \$table->string('featured_image')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->integer('sort_order')->default(0);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
            
            \$table->unique(['slug', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000008_create_room_types_table.php', $roomTypesMigration);
echo "- Room Types tablosu migrasyonu oluşturuldu.\n";

// Oda özellikleri tablosu
$roomAmenitiesMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_amenities', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->string('category')->nullable();
            \$table->string('icon')->nullable();
            \$table->text('description')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->integer('sort_order')->default(0);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_amenities');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000009_create_room_amenities_table.php', $roomAmenitiesMigration);
echo "- Room Amenities tablosu migrasyonu oluşturuldu.\n";

// Odalar tablosu
$roomsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            \$table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            \$table->string('name');
            \$table->string('room_number')->nullable();
            \$table->string('floor')->nullable();
            \$table->text('description')->nullable();
            \$table->text('notes')->nullable();
            \$table->enum('status', ['available', 'occupied', 'maintenance', 'out_of_service'])->default('available');
            \$table->boolean('is_active')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
        
        Schema::create('room_room_amenity', function (Blueprint \$table) {
            \$table->foreignId('room_id')->constrained()->onDelete('cascade');
            \$table->foreignId('room_amenity_id')->constrained()->onDelete('cascade');
            \$table->primary(['room_id', 'room_amenity_id']);
            \$table->timestamps();
        });
        
        Schema::create('room_board_type', function (Blueprint \$table) {
            \$table->foreignId('room_id')->constrained()->onDelete('cascade');
            \$table->foreignId('board_type_id')->constrained()->onDelete('cascade');
            \$table->primary(['room_id', 'board_type_id']);
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_board_type');
        Schema::dropIfExists('room_room_amenity');
        Schema::dropIfExists('rooms');
    }
};
EOT;

File::put($exportDir . '/0001_01_02_000010_create_rooms_table.php', $roomsMigration);
echo "- Rooms tablosu migrasyonu oluşturuldu.\n";

// 5. Rezervasyon modülü migrasyonları
echo "\nRezervasyon modülü migrasyonlarını oluşturuyorum...\n";

// Rezervasyonlar tablosu
$reservationsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint \$table) {
            \$table->id();
            \$table->string('reservation_number')->unique();
            \$table->foreignId('hotel_id')->constrained();
            \$table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            \$table->foreignId('board_type_id')->nullable()->constrained()->nullOnDelete();
            \$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            \$table->date('check_in_date');
            \$table->date('check_out_date');
            \$table->time('check_in_time')->nullable();
            \$table->time('check_out_time')->nullable();
            \$table->integer('adults')->default(1);
            \$table->integer('children')->default(0);
            \$table->integer('infants')->default(0);
            \$table->json('child_ages')->nullable();
            \$table->decimal('total_price', 10, 2);
            \$table->string('currency')->default('TRY');
            \$table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('pending');
            \$table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded', 'cancelled'])->default('pending');
            \$table->text('notes')->nullable();
            \$table->text('special_requests')->nullable();
            \$table->string('source')->nullable()->comment('Online, phone, walk-in, agency, etc.');
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
            
            \$table->index(['check_in_date', 'check_out_date']);
            \$table->index('status');
            \$table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
EOT;

File::put($exportDir . '/0001_01_03_000001_create_reservations_table.php', $reservationsMigration);
echo "- Reservations tablosu migrasyonu oluşturuldu.\n";

// Misafirler tablosu
$guestsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            \$table->string('first_name');
            \$table->string('last_name');
            \$table->string('email')->nullable();
            \$table->string('phone')->nullable();
            \$table->string('nationality')->nullable();
            \$table->string('id_type')->nullable();
            \$table->string('id_number')->nullable();
            \$table->date('birth_date')->nullable();
            \$table->string('gender')->nullable();
            \$table->text('address')->nullable();
            \$table->string('city')->nullable();
            \$table->string('country')->nullable();
            \$table->boolean('is_primary')->default(false);
            \$table->boolean('is_child')->default(false);
            \$table->text('notes')->nullable();
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
EOT;

File::put($exportDir . '/0001_01_03_000002_create_guests_table.php', $guestsMigration);
echo "- Guests tablosu migrasyonu oluşturuldu.\n";

// 6. Fiyatlandırma modülü migrasyonları
echo "\nFiyatlandırma modülü migrasyonlarını oluşturuyorum...\n";

// Rate plans tablosu
$ratePlansMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_plans', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            \$table->foreignId('room_id')->constrained()->onDelete('cascade');
            \$table->foreignId('board_type_id')->constrained()->onDelete('cascade');
            \$table->boolean('is_per_person')->default(true)->comment('true: kişi bazlı, false: ünite bazlı');
            \$table->boolean('status')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
            
            // Her otel-oda-board_type için unique kombinasyon
            \$table->unique(['hotel_id', 'room_id', 'board_type_id'], 'rate_plan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_plans');
    }
};
EOT;

File::put($exportDir . '/0001_01_04_000001_create_rate_plans_table.php', $ratePlansMigration);
echo "- Rate Plans tablosu migrasyonu oluşturuldu.\n";

// Rate periods tablosu
$ratePeriodsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_periods', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('rate_plan_id')->constrained()->onDelete('cascade');
            \$table->date('start_date');
            \$table->date('end_date');
            \$table->decimal('base_price', 10, 2);
            \$table->string('currency')->default('TRY');
            \$table->integer('min_stay')->default(1);
            \$table->integer('max_stay')->nullable();
            \$table->integer('quantity')->default(1)->comment('Available units/rooms');
            \$table->enum('sales_type', ['direct_sale', 'ask_availability', 'inquire_only'])->default('direct_sale');
            \$table->boolean('status')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
            
            \$table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_periods');
    }
};
EOT;

File::put($exportDir . '/0001_01_04_000002_create_rate_periods_table.php', $ratePeriodsMigration);
echo "- Rate Periods tablosu migrasyonu oluşturuldu.\n";

// Rate exceptions tablosu
$rateExceptionsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_exceptions', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('rate_period_id')->constrained()->onDelete('cascade');
            \$table->date('date');
            \$table->decimal('base_price', 10, 2)->nullable();
            \$table->json('prices')->nullable()->comment('Kişi sayısına göre fiyatlar');
            \$table->integer('min_stay')->nullable();
            \$table->integer('quantity')->nullable();
            \$table->enum('sales_type', ['direct_sale', 'ask_availability', 'inquire_only'])->nullable();
            \$table->boolean('status')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
            
            \$table->unique(['rate_period_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_exceptions');
    }
};
EOT;

File::put($exportDir . '/0001_01_04_000003_create_rate_exceptions_table.php', $rateExceptionsMigration);
echo "- Rate Exceptions tablosu migrasyonu oluşturuldu.\n";

// Booking prices tablosu
$bookingPricesMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_prices', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            \$table->foreignId('rate_plan_id')->nullable()->constrained()->nullOnDelete();
            \$table->json('daily_prices')->nullable()->comment('Her gün için fiyat detayları');
            \$table->decimal('room_price', 10, 2)->default(0);
            \$table->decimal('board_price', 10, 2)->default(0);
            \$table->decimal('extra_bed_price', 10, 2)->default(0);
            \$table->decimal('child_price', 10, 2)->default(0);
            \$table->decimal('tax_amount', 10, 2)->default(0);
            \$table->decimal('discount_amount', 10, 2)->default(0);
            \$table->string('discount_code')->nullable();
            \$table->string('discount_type')->nullable();
            \$table->decimal('total_price', 10, 2);
            \$table->string('currency')->default('TRY');
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_prices');
    }
};
EOT;

File::put($exportDir . '/0001_01_04_000004_create_booking_prices_table.php', $bookingPricesMigration);
echo "- Booking Prices tablosu migrasyonu oluşturuldu.\n";

// 7. OTA/Entegrasyon modülü migrasyonları
echo "\nOTA/Entegrasyon modülü migrasyonlarını oluşturuyorum...\n";

// Channels tablosu
$channelsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channels', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->enum('type', ['ota', 'gds', 'channel_manager', 'direct', 'other'])->default('ota');
            \$table->text('description')->nullable();
            \$table->string('logo')->nullable();
            \$table->json('settings')->nullable();
            \$table->json('credentials')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->boolean('is_enabled')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
EOT;

File::put($exportDir . '/0001_01_05_000001_create_channels_table.php', $channelsMigration);
echo "- Channels tablosu migrasyonu oluşturuldu.\n";

// XML Mappings tablosu
$xmlMappingsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xml_mappings', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('channel_id')->constrained()->onDelete('cascade');
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->enum('direction', ['import', 'export', 'both'])->default('import');
            \$table->enum('entity_type', ['hotel', 'room', 'rate', 'availability', 'reservation', 'other'])->default('hotel');
            \$table->text('description')->nullable();
            \$table->string('xml_root_path')->nullable();
            \$table->json('field_mappings')->nullable();
            \$table->json('value_transformations')->nullable();
            \$table->json('sample_data')->nullable();
            \$table->text('template_content')->nullable();
            \$table->string('template_format')->default('xml');
            \$table->boolean('is_active')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xml_mappings');
    }
};
EOT;

File::put($exportDir . '/0001_01_05_000002_create_xml_mappings_table.php', $xmlMappingsMigration);
echo "- XML Mappings tablosu migrasyonu oluşturuldu.\n";

// 8. Tema Yönetimi migrasyonları
echo "\nTema Yönetimi modülü migrasyonlarını oluşturuyorum...\n";

// Theme Settings tablosu
$themeSettingsMigration = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint \$table) {
            \$table->id();
            \$table->string('key')->unique();
            \$table->text('value')->nullable();
            \$table->string('type')->default('string'); // string, integer, boolean, json, color, image
            \$table->string('group')->default('general'); // colors, layout, typography, seo, social, etc.
            \$table->boolean('is_public')->default(true);
            \$table->boolean('is_active')->default(true);
            \$table->string('tenant_id')->nullable();
            \$table->softDeletes();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
EOT;

File::put($exportDir . '/0001_01_06_000001_create_theme_settings_table.php', $themeSettingsMigration);
echo "- Theme Settings tablosu migrasyonu oluşturuldu.\n";

// 9. Tüm migrasyonları yeni dizine kopyala
echo "\nTüm migrasyonlar başarıyla oluşturuldu.\n";
echo "Şimdi bu migrasyonları production dizinine taşımak ister misiniz? (evet/hayır): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) === 'evet') {
    echo "\nVarolan migrasyonlarınız için bir yedek almalısınız. Yedek almak istiyor musunuz? (evet/hayır): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    
    if (strtolower($line) === 'evet') {
        $backupDir = __DIR__ . '/database/migrations_backup_' . date('Y_m_d_His');
        File::makeDirectory($backupDir, 0755, true);
        
        // Mevcut migrasyonları yedekle
        $oldMigrations = File::files(__DIR__ . '/database/migrations');
        foreach ($oldMigrations as $file) {
            File::copy($file->getPathname(), $backupDir . '/' . $file->getFilename());
        }
        
        echo "Yedek başarıyla alındı: $backupDir\n";
    }
    
    // Temiz migrasyonları aktif dizine kopyala
    File::cleanDirectory(__DIR__ . '/database/migrations');
    $newMigrations = File::files($exportDir);
    foreach ($newMigrations as $file) {
        File::copy($file->getPathname(), __DIR__ . '/database/migrations/' . $file->getFilename());
    }
    
    echo "Yeni migrasyonlar başarıyla aktif dizine kopyalandı.\n";
}

echo "\nTüm işlemler tamamlandı. Şimdi temiz migration'larınızı şu şekilde çalıştırabilirsiniz:\n";
echo "php artisan migrate:fresh --seed\n";
echo "\nBu komut tüm veritabanını temizleyecek ve sıfırdan oluşturacaktır.\n";
echo "Veya yeni bir veritabanında test etmek isterseniz:\n";
echo "php artisan migrate\n";