<?php

namespace App\Plugins\MenuManager\View\Components;

use App\Plugins\MenuManager\Models\Menu as MenuModel;
use App\Plugins\MenuManager\Services\MenuManagerService;
use Illuminate\View\Component;

class MegaMenu extends Component
{
    /**
     * The menu object or slug.
     *
     * @var MenuModel|string|null
     */
    public $menu;
    
    /**
     * Menu location.
     *
     * @var string|null
     */
    public $location;
    
    /**
     * Additional CSS class.
     *
     * @var string
     */
    public $class;
    
    /**
     * Menu ID.
     *
     * @var string
     */
    public $id;
    
    /**
     * Wrapper class.
     *
     * @var string
     */
    public $wrapperClass;
    
    /**
     * Create a new component instance.
     *
     * @param string|null $slug Menu slug to load
     * @param string|null $location Menu location to load (alternative to slug)
     * @param string $class Additional CSS class
     * @param string $id Menu ID attribute
     * @param string $wrapperClass Wrapper div class
     */
    public function __construct(
        ?string $slug = null,
        ?string $location = null,
        string $class = '',
        string $id = '',
        string $wrapperClass = 'mega-menu-wrapper'
    ) {
        $this->location = $location;
        $this->class = $class;
        $this->id = $id;
        $this->wrapperClass = $wrapperClass;
        
        // Get menu service
        $menuService = app(MenuManagerService::class);
        
        // Get menu by location or slug
        if ($location) {
            $this->menu = $menuService->getMenuByLocation($location);
        } elseif ($slug) {
            $this->menu = $menuService->getMenu($slug);
        } else {
            $this->menu = null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('vendor.menu-manager.templates.mega');
    }
}