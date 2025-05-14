<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tms\Core\Models\Agency;
use Tms\Core\Models\BaseModel;
use Tms\Core\Http\Middleware\TenantMiddleware;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TenantMiddlewareTest extends TestCase
{
    // use RefreshDatabase;

    #[Test]
    public function tenant_middleware_redirects_when_no_tenant_selected()
    {
        // TenantMiddleware'i oluştur
        $middleware = new TenantMiddleware();
        
        // Sahte bir istek oluştur
        $request = Request::create('/admin/dashboard', 'GET');
        
        // Test için sahte bir route ekle
        $request->setRouteResolver(function () {
            return new \stdClass(); // Route sınıfı simulasyonu
        });
        
        // Route name özelliği ekle
        $route = $request->route();
        $route->name = 'filament.admin.pages.dashboard';
        
        // Middleware'ı çalıştır
        $response = $middleware->handle($request, function ($req) {
            // Bu fonksiyon çağrılmamalı, çünkü middleware tenant seçilmediği için yönlendirme yapmalı
            return 'Çalıştı';
        });
        
        // Response türünü ve yönlendirme URL'ini kontrol et
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('filament.admin.pages.tenant-select'), $response->headers->get('Location'));
    }
    
    #[Test]
    public function tenant_middleware_allows_access_with_selected_tenant()
    {
        // Tenant seçildiğini simüle etmek için session'a değer ekle
        session(['tenant_id' => 1]);
        
        // TenantMiddleware'i oluştur
        $middleware = new TenantMiddleware();
        
        // Sahte bir istek oluştur
        $request = Request::create('/admin/dashboard', 'GET');
        
        // Test için sahte bir route ekle
        $request->setRouteResolver(function () {
            return new \stdClass(); // Route sınıfı simulasyonu
        });
        
        // Route name özelliği ekle
        $route = $request->route();
        $route->name = 'filament.admin.pages.dashboard';
        
        // Middleware'ı çalıştır
        $response = $middleware->handle($request, function ($req) {
            // Şimdi bu fonksiyon çağrılmalı, çünkü tenant seçilmiş durumda
            return 'Çalıştı';
        });
        
        // Tepkiyi kontrol et
        $this->assertEquals('Çalıştı', $response);
        
        // Session'ı temizle
        session()->forget('tenant_id');
    }
    
    #[Test]
    public function tenant_middleware_bypasses_tenant_select_page()
    {
        // TenantMiddleware'i oluştur
        $middleware = new TenantMiddleware();
        
        // Sahte bir istek oluştur
        $request = Request::create('/admin/tenant-select', 'GET');
        
        // Test için sahte bir route ekle
        $request->setRouteResolver(function () {
            return new \stdClass(); // Route sınıfı simulasyonu
        });
        
        // Route name özelliği ekle
        $route = $request->route();
        $route->name = 'filament.admin.pages.tenant-select';
        
        // Middleware'ı çalıştır
        $response = $middleware->handle($request, function ($req) {
            return 'Tenant Seçim Sayfası';
        });
        
        // Bypass edilmesi gereken sayfalar için yönlendirme olmamalı
        $this->assertEquals('Tenant Seçim Sayfası', $response);
    }
    
    #[Test]
    public function tenant_middleware_bypasses_asset_requests()
    {
        // TenantMiddleware'i oluştur
        $middleware = new TenantMiddleware();
        
        // Sahte bir asset isteği oluştur
        $request = Request::create('/assets/js/app.js', 'GET');
        
        // Test için sahte bir route ekle
        $request->setRouteResolver(function () {
            return new \stdClass(); // Route sınıfı simulasyonu
        });
        
        // Route name özelliği ekle (genellikle asset route'ları isimsizdir)
        $route = $request->route();
        $route->name = null;
        
        // Middleware'ı çalıştır
        $response = $middleware->handle($request, function ($req) {
            return 'Asset İçeriği';
        });
        
        // Asset istekleri için yönlendirme olmamalı
        $this->assertEquals('Asset İçeriği', $response);
    }
}