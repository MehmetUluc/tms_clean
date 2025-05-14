<?php

use Illuminate\Support\Facades\Route;
use App\Plugins\MenuManager\Models\Menu;

Route::middleware(['web'])->group(function () {
    // Menu preview route
    Route::get('/menu-preview/{menu}', function($menu) {
        $menuModel = Menu::where('slug', $menu)
            ->orWhere('id', $menu)
            ->with(['items' => function ($query) {
                $query->where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children');
            }])
            ->firstOrFail();
            
        return view('vendor.menu-manager.preview', [
            'menu' => $menuModel
        ]);
    })->name('menu-manager.preview');
    
    // Mega Menu demo route
    Route::get('/mega-menu-demo/{menu}', function($menu) {
        $menuModel = Menu::where('slug', $menu)
            ->orWhere('id', $menu)
            ->with(['items' => function ($query) {
                $query->where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children');
            }])
            ->firstOrFail();
            
        return view('vendor.menu-manager.demo-mega-menu', [
            'menu' => $menuModel
        ]);
    })->name('menu-manager.mega-demo');
});