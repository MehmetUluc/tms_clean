<?php

// Load the Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "=== TMS Şema Temizleme Aracı ===\n\n";

// MySQL-schema.sql dosyasını boşalt
$schemaFile = __DIR__ . '/database/schema/mysql-schema.sql';

if (File::exists($schemaFile)) {
    // Dosyanın içeriğini boşalt, sadece en basit MySQL export yapısını bırak
    $content = <<<EOT
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
EOT;

    File::put($schemaFile, $content);
    echo "Schema dosyası temizlendi: $schemaFile\n";
} else {
    echo "Schema dosyası bulunamadı: $schemaFile\n";
}

echo "\nŞimdi fresh migration ile temiz bir kurulum yapabilirsiniz:\n";
echo "php artisan migrate:fresh --seed\n";