<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Test veritabanını doğrula
        try {
            DB::connection()->getPdo();
            echo "Veritabanı bağlantısı başarılı: " . DB::connection()->getDatabaseName() . "\n";
        } catch (\Exception $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage() . "\n");
        }
    }
}
