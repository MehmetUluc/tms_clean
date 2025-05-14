<?php

namespace App\Plugins\MenuManager\Http\Livewire;

use App\Plugins\MenuManager\Models\Menu;
use App\Plugins\MenuManager\Services\MenuManagerService;
use Livewire\Component;

class MenuPreview extends Component
{
    public $menu;
    public $menuId;
    public $selectedType = 'default';
    public $selectedDevice = 'desktop';
    public $backgroundStyle = '';
    public $showBackground = false;
    public $selectedBackgroundColor = '#ffffff';
    public $showCode = false;
    
    protected $listeners = [
        'refreshPreview' => '$refresh',
    ];
    
    /**
     * Mount the component.
     *
     * @param  Menu  $menu
     * @return void
     */
    public function mount(Menu $menu)
    {
        $this->menu = $menu;
        $this->menuId = $menu->id;
        $this->selectedType = $menu->type ?? 'default';
    }
    
    /**
     * Toggle background settings.
     *
     * @return void
     */
    public function toggleBackground()
    {
        $this->showBackground = !$this->showBackground;
        $this->updateBackgroundStyle();
    }
    
    /**
     * Update background color.
     *
     * @param  string  $color
     * @return void
     */
    public function updateBackgroundColor($color)
    {
        $this->selectedBackgroundColor = $color;
        $this->updateBackgroundStyle();
    }
    
    /**
     * Update background style.
     *
     * @return void
     */
    protected function updateBackgroundStyle()
    {
        if ($this->showBackground) {
            $this->backgroundStyle = "background-color: {$this->selectedBackgroundColor};";
        } else {
            $this->backgroundStyle = '';
        }
    }
    
    /**
     * Toggle code view.
     *
     * @return void
     */
    public function toggleCode()
    {
        $this->showCode = !$this->showCode;
    }
    
    /**
     * Change menu type.
     *
     * @param  string  $type
     * @return void
     */
    public function changeType($type)
    {
        $this->selectedType = $type;
    }
    
    /**
     * Change device type.
     *
     * @param  string  $device
     * @return void
     */
    public function changeDevice($device)
    {
        $this->selectedDevice = $device;
    }
    
    /**
     * Get implementation code.
     *
     * @return string
     */
    public function getImplementationCode()
    {
        return "<!-- Menu Component -->\n<x-menu-manager::menu slug=\"{$this->menu->slug}\" type=\"{$this->selectedType}\" />";
    }
    
    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Get fresh data to prevent stale cache
        $menu = app(MenuManagerService::class)->getMenu($this->menu->slug, true, false);
        
        return view('menu-manager::livewire.menu-preview', [
            'menuData' => $menu,
            'menuTypes' => config('menu-manager.types'),
            'deviceTypes' => [
                'desktop' => 'Desktop View',
                'tablet' => 'Tablet View (768px)',
                'mobile' => 'Mobile View (375px)',
            ],
            'implementationCode' => $this->getImplementationCode(),
        ]);
    }
}