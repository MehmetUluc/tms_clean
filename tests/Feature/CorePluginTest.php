<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tms\Core\Models\Agency;
use Tms\Core\Models\Role;
use Tms\Core\Models\Permission;

class CorePluginTest extends TestCase
{
    // Yalnızca test metotları çalıştıralım, veritabanını etkilemeden
    // use RefreshDatabase;

    #[Test]
    public function it_can_create_an_agency()
    {
        $agency = Agency::create([
            'name' => 'Test Agency',
            'code' => 'TEST001',
            'email' => 'test@example.com',
            'phone' => '5551234567',
            'is_active' => true,
            'slug' => 'test-agency',
            'currency' => 'USD'
        ]);

        $this->assertNotNull($agency);
        $this->assertEquals('Test Agency', $agency->name);
        $this->assertEquals('test-agency', $agency->slug);
    }

    #[Test]
    public function it_can_create_roles_and_permissions()
    {
        // Permissions modeli veritabanındaki sütunlara uyumlu hale getirildi
        $permission = Permission::create([
            'name' => 'view_agencies',
            'slug' => 'view-agencies', 
            'description' => 'View all agencies',
            'group' => 'agencies'
        ]);

        $role = Role::create([
            'name' => 'Manager',
            'slug' => 'manager',
            'description' => 'Agency manager',
        ]);

        $role->permissions()->attach($permission);

        $this->assertCount(1, $role->permissions);
        $this->assertEquals('view_agencies', $role->permissions->first()->name);
    }

    #[Test]
    public function it_has_tenant_mode_disabled_for_agency_model()
    {
        // Yansıma (Reflection) kullanarak static özelliği kontrol et
        $reflection = new \ReflectionClass(Agency::class);
        $property = $reflection->getProperty('hasTenant');
        $property->setAccessible(true);
        
        $this->assertFalse($property->getValue());
    }

    #[Test]
    public function it_properly_handles_logo_url_accessor()
    {
        $agency = Agency::create([
            'name' => 'Agency with Logo',
            'code' => 'LOGO001',
            'email' => 'logo@example.com',
            'logo' => 'agencies/logo.png',
            'is_active' => true,
            'slug' => 'agency-with-logo'
        ]);

        $this->assertStringContainsString('storage/agencies/logo.png', $agency->logo_url);

        $agency->logo = 'https://example.com/logo.png';
        $agency->save();

        $this->assertEquals('https://example.com/logo.png', $agency->logo_url);
    }
}