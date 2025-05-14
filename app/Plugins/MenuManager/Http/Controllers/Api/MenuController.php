<?php

namespace App\Plugins\MenuManager\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\MenuManager\Services\MenuManagerService;

class MenuController extends Controller
{
    protected $menuService;
    
    public function __construct(MenuManagerService $menuService)
    {
        $this->menuService = $menuService;
    }
    
    /**
     * Get a menu by its location (header, footer, etc.)
     */
    public function getByLocation($location)
    {
        $menu = $this->menuService->getMenuByLocation($location);
        
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'menu' => $menu
        ]);
    }
    
    /**
     * Get a menu by its slug
     */
    public function getBySlug($slug)
    {
        $menu = $this->menuService->getMenu($slug);
        
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'menu' => $menu
        ]);
    }
    
    /**
     * List all active menus
     */
    public function index()
    {
        $menus = $this->menuService->getAllMenus(true);
        
        return response()->json([
            'success' => true,
            'menus' => $menus
        ]);
    }
}