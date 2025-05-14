<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bu migration artık kullanılmıyor, tablolar oluşturulurken tenant_id zaten ekleniyor
        // Foreign key constraint olmadan

        // Hiçbir işlem yapma
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri dönüş işlemi yok
    }
};