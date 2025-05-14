<?php

namespace App\Plugins\MenuManager\Http\Controllers;

use App\Plugins\MenuManager\Models\Menu;
use App\Plugins\MenuManager\Services\MenuManagerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuPreviewController extends Controller
{
    protected $menuService;

    public function __construct(MenuManagerService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Show the menu preview page.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function preview($slug)
    {
        $menu = $this->menuService->getMenu($slug, true);
        
        if (!$menu) {
            abort(404, 'Menu not found');
        }
        
        return view('menu-manager::preview', [
            'menu' => $menu,
            'menuTypes' => config('menu-manager.types'),
            'deviceTypes' => [
                'desktop' => 'Desktop View',
                'tablet' => 'Tablet View',
                'mobile' => 'Mobile View',
            ],
        ]);
    }
    
    /**
     * Render a specific menu type for AJAX preview.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function renderPreview(Request $request)
    {
        $menuId = $request->input('menu_id');
        $menuType = $request->input('menu_type', 'default');
        
        $menu = Menu::with(['items' => function ($query) {
            $query->orderBy('order')
                ->with('children');
        }])->findOrFail($menuId);
        
        return view('menu-manager::templates.' . $menuType, [
            'menu' => $menu,
            'class' => 'preview-menu ' . $request->input('class', ''),
            'id' => 'preview-menu-' . $menu->id,
        ]);
    }
}