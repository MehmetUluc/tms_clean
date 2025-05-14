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
        // Menus tablosuna tenant_id ekleme
        if (!Schema::hasColumn('menus', 'tenant_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                // tenants tablosu olmadığı için foreign key constraint eklemiyoruz
            });
        }

        // Menu_items tablosuna tenant_id ekleme
        if (!Schema::hasColumn('menu_items', 'tenant_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                // tenants tablosu olmadığı için foreign key constraint eklemiyoruz
            });
        }

        // Menu_item_templates tablosuna tenant_id ekleme
        if (!Schema::hasColumn('menu_item_templates', 'tenant_id')) {
            Schema::table('menu_item_templates', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable();
                // tenants tablosu olmadığı için foreign key constraint eklemiyoruz
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menus tablosundan tenant_id kaldırma
        if (Schema::hasColumn('menus', 'tenant_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Menu_items tablosundan tenant_id kaldırma
        if (Schema::hasColumn('menu_items', 'tenant_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Menu_item_templates tablosundan tenant_id kaldırma
        if (Schema::hasColumn('menu_item_templates', 'tenant_id')) {
            Schema::table('menu_item_templates', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
};