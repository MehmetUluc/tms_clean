<?php

namespace App\Plugins\MenuManager\Filament\Resources\MenuResource\Pages;

use App\Plugins\MenuManager\Filament\Resources\MenuResource;
use App\Plugins\MenuManager\Models\Menu;
use App\Plugins\MenuManager\Models\MenuItem;
use App\Plugins\MenuManager\Services\MenuManagerService;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

class BuildMenu extends Page
{
    protected static string $resource = MenuResource::class;

    protected static string $view = 'vendor.menu-manager.pages.build-menu';
    
    public ?Menu $menu = null;
    
    public ?array $menuItems = null;
    
    public $menuId;
    
    public $newMenuItem = [
        'title' => '',
        'url' => '',
        'link_type' => 'url',
        'target' => '_self',
        'parent_id' => null,
    ];
    
    public function mount($record): void
    {
        $this->menu = Menu::with(['items' => function ($query) {
            $query->whereNull('parent_id')->orderBy('order')->with('children');
        }])->findOrFail($record);
        
        $this->menuId = $this->menu->id;
        $this->loadMenuItems();
    }
    
    protected function loadMenuItems(): void
    {
        $this->menuItems = MenuItem::where('menu_id', $this->menuId)
            ->orderBy('order')
            ->get()
            ->toArray();
    }
    
    public function getTitle(): string|Htmlable
    {
        return "Build Menu: {$this->menu->name}";
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_edit')
                ->label('Back to Edit')
                ->url(fn () => $this->getResource()::getUrl('edit', ['record' => $this->menu])),
            Actions\Action::make('preview')
                ->label('Preview Menu')
                ->button()
                ->color('warning')
                ->action(fn () => $this->previewMenu()),
        ];
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Components\Select::make('link_type')
                    ->options(config('menu-manager.link_types'))
                    ->default('url')
                    ->live()
                    ->required(),
                Components\TextInput::make('url')
                    ->required()
                    ->visible(fn (Get $get): bool => $get('link_type') === 'url')
                    ->maxLength(255),
                Components\Select::make('route_name')
                    ->options($this->getRoutes())
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get('link_type') === 'route'),
                Components\Select::make('model_type')
                    ->options(config('menu-manager.linkable_models'))
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get('link_type') === 'model')
                    ->live(),
                Components\Select::make('model_id')
                    ->options(function (Get $get) {
                        $modelType = $get('model_type');
                        if (!$modelType) return [];
                        
                        if (class_exists($modelType)) {
                            return $modelType::all()->pluck('name', 'id');
                        }
                        
                        return [];
                    })
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get('link_type') === 'model' && $get('model_type')),
                Components\Select::make('target')
                    ->options(config('menu-manager.targets'))
                    ->default('_self')
                    ->required(),
                Components\TextInput::make('icon')
                    ->maxLength(255),
                Components\Select::make('parent_id')
                    ->label('Parent Item')
                    ->options(function () {
                        return MenuItem::where('menu_id', $this->menuId)
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->placeholder('No Parent (Root Level)'),
                Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Components\Toggle::make('is_featured')
                    ->label('Featured')
                    ->default(false),
            ]);
    }
    
    protected function getRoutes(): array
    {
        $routes = Route::getRoutes();
        $routeNames = [];
        
        foreach ($routes as $route) {
            if ($route->getName()) {
                $routeNames[$route->getName()] = $route->getName() . ' (' . $route->uri() . ')';
            }
        }
        
        return $routeNames;
    }
    
    public function createMenuItem()
    {
        $data = $this->form->getState();
        
        // Format data based on link type
        if ($data['link_type'] !== 'url') {
            $data['url'] = null;
        }
        
        if ($data['link_type'] !== 'route') {
            $data['route_name'] = null;
        }
        
        if ($data['link_type'] !== 'model') {
            $data['model_type'] = null;
            $data['model_id'] = null;
        }
        
        // Find the highest order number for this level
        $maxOrder = MenuItem::where('menu_id', $this->menuId)
            ->where('parent_id', $data['parent_id'])
            ->max('order');
            
        // Create the menu item
        $data['menu_id'] = $this->menuId;
        $data['order'] = ($maxOrder ?? 0) + 1;
        
        MenuItem::create($data);
        
        // Clear form and reload items
        $this->form->fill();
        $this->loadMenuItems();
        
        // Clear cache
        app(MenuManagerService::class)->clearCache($this->menu->slug);
        
        Notification::make()
            ->title('Menu item created successfully')
            ->success()
            ->send();
    }
    
    #[On('delete-menu-item')]
    public function deleteMenuItem($itemId): void
    {
        $item = MenuItem::findOrFail($itemId);
        
        // Handle children - either delete them or move them up a level
        foreach ($item->children as $child) {
            $child->update([
                'parent_id' => $item->parent_id,
            ]);
        }
        
        $item->delete();
        $this->loadMenuItems();
        
        // Clear cache
        app(MenuManagerService::class)->clearCache($this->menu->slug);
        
        Notification::make()
            ->title('Menu item deleted successfully')
            ->success()
            ->send();
    }
    
    #[On('update-item-order')]
    public function updateItemOrder($itemsData): void
    {
        // Process the updated order data
        app(MenuManagerService::class)->reorderMenuItems($itemsData);
        
        $this->loadMenuItems();
        
        Notification::make()
            ->title('Menu order updated successfully')
            ->success()
            ->send();
    }
    
    protected function previewMenu(): void
    {
        // Redirect to preview or show modal
        Notification::make()
            ->title('Preview feature coming soon')
            ->warning()
            ->send();
    }
}