# Çok Yönlü Menü Yönetim Sistemi Planı

Bu doküman, basit dropdown menülerden karmaşık mega menülere kadar her türlü menü yapısını destekleyebilen esnek bir menü yönetim sistemi için teknik plan ve geliştirme yol haritasını içerir.

## 1. Veri Modeli ve Veritabanı Yapısı

### Veritabanı Tabloları

- **menus**: Ana menü yapılandırmalarını içerir
  - `id`: Ana menü kimliği
  - `name`: Menü adı
  - `slug`: Menü slug'ı
  - `description`: Menü açıklaması
  - `location`: Menünün konumu (header, footer vb.)
  - `settings`: JSON formatında menü ayarları
  - `status`: Menü durumu (aktif/pasif)
  - `created_at`, `updated_at`: Zaman damgaları

- **menu_items**: Menü öğelerini içerir
  - `id`: Menü öğesi kimliği
  - `menu_id`: Ait olduğu menü
  - `parent_id`: Üst menü öğesi (null ise kök öğesidir)
  - `title`: Menü öğesi başlığı
  - `url`: Bağlantı URL'si
  - `target`: Bağlantı hedefi (_self, _blank vb.)
  - `icon`: Menü öğesi ikonu
  - `order`: Sıralama değeri
  - `type`: Öğe tipi (link, dropdown, mega_menu, html, dynamic)
  - `status`: Öğe durumu (aktif/pasif)
  - `permissions`: Erişim izinleri (JSON)
  - `template`: Kullanılacak şablon kimliği/slug'ı
  - `settings`: JSON formatında öğe ayarları
  - `locale`: Dil kodu
  - `created_at`, `updated_at`: Zaman damgaları

- **menu_item_templates**: Menü öğesi şablonlarını içerir
  - `id`: Şablon kimliği
  - `name`: Şablon adı
  - `slug`: Şablon slug'ı
  - `description`: Şablon açıklaması
  - `html_template`: HTML şablon kodu
  - `settings_schema`: Ayar şeması (JSON)
  - `type`: Şablon tipi (dropdown, mega_menu vb.)
  - `created_at`, `updated_at`: Zaman damgaları

### Model Sınıfları

```php
// Menu.php
class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'location',
        'settings',
        'status',
    ];
    
    protected $casts = [
        'settings' => 'array',
        'status' => 'boolean',
    ];
    
    // İlişkiler
    public function items() {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order');
    }
    
    // Recursive tüm menü öğelerini getirme
    public function allItems() {
        return $this->items()->with('allChildren');
    }
    
    // Konuma göre menü bulma
    public static function findByLocation($location) {
        return static::where('location', $location)
            ->where('status', true)
            ->first();
    }
}

// MenuItem.php
class MenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'target',
        'icon',
        'order',
        'type',
        'status',
        'permissions',
        'template',
        'settings',
        'locale',
    ];
    
    protected $casts = [
        'permissions' => 'array',
        'settings' => 'array',
        'status' => 'boolean',
    ];
    
    // İlişkiler
    public function menu() {
        return $this->belongsTo(Menu::class);
    }
    
    public function parent() {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }
    
    public function children() {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }
    
    // Recursive tüm alt menü öğelerini getirme
    public function allChildren() {
        return $this->children()->with('allChildren');
    }
    
    // Scope'lar
    public function scopeActive($query) {
        return $query->where('status', true);
    }
    
    // Yardımcı metodlar
    public function hasChildren() {
        return $this->children()->count() > 0;
    }
    
    public function getTemplateInstance() {
        if (!$this->template) {
            return null;
        }
        
        return MenuItemTemplate::where('slug', $this->template)->first();
    }
}

// MenuItemTemplate.php
class MenuItemTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'html_template',
        'settings_schema',
        'type',
    ];
    
    protected $casts = [
        'settings_schema' => 'array',
    ];
    
    public function getSettingsFields() {
        return $this->settings_schema ?: [];
    }
}
```

## 2. Filament Admin Panel Entegrasyonu

### Menu Resource

```php
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Resources\Form;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    
    protected static ?string $navigationLabel = 'Menüler';
    
    protected static ?string $navigationGroup = 'Site Yönetimi';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Menü Adı')
                            ->required(),
                            
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(Menu::class, 'slug', fn ($record) => $record),
                            
                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3),
                            
                        Select::make('location')
                            ->label('Konum')
                            ->options([
                                'main-header' => 'Ana Header',
                                'footer' => 'Footer',
                                'mobile' => 'Mobil Menü',
                                'sidebar' => 'Yan Menü',
                                'user-menu' => 'Kullanıcı Menüsü',
                            ]),
                            
                        Toggle::make('status')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Menü Adı')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('location')
                    ->label('Konum'),
                    
                IconColumn::make('status')
                    ->label('Durum')
                    ->boolean(),
                    
                TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->label('Konum')
                    ->options([
                        'main-header' => 'Ana Header',
                        'footer' => 'Footer',
                        'mobile' => 'Mobil Menü',
                        'sidebar' => 'Yan Menü',
                        'user-menu' => 'Kullanıcı Menüsü',
                    ]),
                    
                TernaryFilter::make('status')
                    ->label('Durum'),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('manage_items')
                        ->label('Menü Öğelerini Yönet')
                        ->url(fn ($record) => MenuEditorPage::getUrl(['menu' => $record->id]))
                        ->icon('heroicon-o-pencil-square'),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\MenuItemsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
```

### MenuEditor Custom Page

```php
class MenuEditorPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static string $view = 'menu-manager::menu-editor';
    
    protected static ?string $slug = 'menu-editor';
    
    protected static ?string $title = 'Menü Düzenleyici';
    
    public $menu = null;
    
    public function mount($menu)
    {
        $this->menu = Menu::findOrFail($menu);
    }
}
```

### Menu Editor Component

```php
class MenuEditorComponent extends Component
{
    public $menu;
    public $items = [];
    public $availableTemplates = [];
    public $editingItem = null;
    
    protected $listeners = ['updateMenuOrder'];
    
    public function mount(Menu $menu)
    {
        $this->menu = $menu;
        $this->loadItems();
        $this->loadTemplates();
    }
    
    public function loadItems()
    {
        $this->items = $this->menu->allItems()->get()->toTree();
    }
    
    public function loadTemplates()
    {
        $this->availableTemplates = MenuItemTemplate::all()->pluck('name', 'slug')->toArray();
    }
    
    public function updateMenuOrder($orderedItems)
    {
        // JSON olarak gelen sıralamayı işle ve veritabanında güncelle
        $this->processOrdering($orderedItems);
        
        $this->loadItems();
        Notification::make()
            ->title('Menü sıralaması güncellendi')
            ->success()
            ->send();
    }
    
    public function editItem($itemId)
    {
        $this->editingItem = MenuItem::find($itemId);
    }
    
    public function saveItem()
    {
        $this->validate([
            'editingItem.title' => 'required',
            'editingItem.type' => 'required',
        ]);
        
        $this->editingItem->save();
        
        $this->loadItems();
        $this->editingItem = null;
        
        Notification::make()
            ->title('Menü öğesi kaydedildi')
            ->success()
            ->send();
    }
    
    public function addNewItem()
    {
        $newItem = new MenuItem([
            'menu_id' => $this->menu->id,
            'title' => 'Yeni Menü Öğesi',
            'type' => 'link',
            'url' => '#',
            'status' => true,
            'order' => MenuItem::where('menu_id', $this->menu->id)
                ->whereNull('parent_id')
                ->count() + 1,
        ]);
        
        $newItem->save();
        
        $this->loadItems();
        $this->editItem($newItem->id);
        
        Notification::make()
            ->title('Yeni menü öğesi eklendi')
            ->success()
            ->send();
    }
    
    public function deleteItem($itemId)
    {
        $item = MenuItem::find($itemId);
        
        if ($item) {
            // Önce tüm alt öğeleri sil
            $this->deleteChildItems($item);
            
            // Sonra ana öğeyi sil
            $item->delete();
            
            $this->loadItems();
            
            Notification::make()
                ->title('Menü öğesi silindi')
                ->success()
                ->send();
        }
    }
    
    protected function deleteChildItems($item)
    {
        // Recursively tüm alt öğeleri sil
        foreach ($item->children as $child) {
            $this->deleteChildItems($child);
            $child->delete();
        }
    }
    
    protected function processOrdering($items, $parentId = null, $order = 0)
    {
        foreach ($items as $i => $itemData) {
            $item = MenuItem::find($itemData['id']);
            
            if ($item) {
                $item->update([
                    'parent_id' => $parentId,
                    'order' => $i + 1,
                ]);
                
                if (isset($itemData['children']) && !empty($itemData['children'])) {
                    $this->processOrdering($itemData['children'], $item->id);
                }
            }
        }
    }
    
    public function render()
    {
        return view('menu-manager::components.menu-editor', [
            'menu' => $this->menu,
            'items' => $this->items,
            'templates' => $this->availableTemplates,
        ]);
    }
}
```

## 3. Menü Şablonları ve Tipler

### Şablon Tanımları

```php
// Standart dropdown şablonu
[
    'name' => 'Basit Dropdown',
    'slug' => 'simple_dropdown',
    'description' => 'Standart dropdown menü',
    'html_template' => '<div class="dropdown">
        <button class="dropdown-toggle">{{ $item->title }}</button>
        <div class="dropdown-menu">
            @foreach($item->children as $child)
                @include("menu::item", ["item" => $child])
            @endforeach
        </div>
    </div>',
    'settings_schema' => [
        'dropdown_alignment' => [
            'type' => 'select',
            'options' => ['left', 'right', 'center'],
            'default' => 'left',
            'label' => 'Açılır Menü Hizalaması'
        ],
        'hover_effect' => [
            'type' => 'boolean',
            'default' => true,
            'label' => 'Hover Efekti'
        ]
    ],
    'type' => 'dropdown'
]

// Mega menü şablonu
[
    'name' => 'Mega Menü 3 Sütun',
    'slug' => 'mega_menu_3_columns',
    'description' => '3 sütunlu mega menü',
    'html_template' => '<div class="mega-menu-wrapper">
        <button class="mega-menu-toggle">{{ $item->title }}</button>
        <div class="mega-menu-container">
            <div class="mega-menu-grid mega-menu-3-columns">
                @php
                    $columns = array_chunk($item->children->toArray(), ceil(count($item->children) / 3));
                @endphp
                
                @foreach($columns as $columnItems)
                    <div class="mega-menu-column">
                        @foreach($columnItems as $child)
                            @include("menu::item", ["item" => $child])
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>',
    'settings_schema' => [
        'background_color' => [
            'type' => 'color',
            'default' => '#ffffff',
            'label' => 'Arkaplan Rengi'
        ],
        'column_width' => [
            'type' => 'select',
            'options' => ['equal', 'auto', 'custom'],
            'default' => 'equal',
            'label' => 'Sütun Genişliği'
        ],
        'custom_column_widths' => [
            'type' => 'text',
            'default' => '33% 33% 33%',
            'label' => 'Özel Sütun Genişlikleri',
            'description' => 'Sütun genişliklerini belirtin (örn: 25% 50% 25%)'
        ],
        'show_featured_content' => [
            'type' => 'boolean',
            'default' => false,
            'label' => 'Öne Çıkan İçerik Göster'
        ],
        'featured_content_type' => [
            'type' => 'select',
            'options' => ['products', 'posts', 'html'],
            'default' => 'products',
            'label' => 'Öne Çıkan İçerik Tipi'
        ],
        'featured_content_query' => [
            'type' => 'textarea',
            'default' => '{"limit": 3, "featured": true}',
            'label' => 'İçerik Sorgusu (JSON)'
        ]
    ],
    'type' => 'mega_menu'
]

// HTML içeriği şablonu
[
    'name' => 'Özel HTML İçeriği',
    'slug' => 'custom_html',
    'description' => 'Özel HTML içeriği için şablon',
    'html_template' => '{!! $item->settings["html_content"] ?? "" !!}',
    'settings_schema' => [
        'html_content' => [
            'type' => 'code_editor',
            'language' => 'html',
            'default' => '<div class="custom-content">Özel İçerik</div>',
            'label' => 'HTML İçeriği'
        ]
    ],
    'type' => 'html'
]

// Dinamik içerik şablonu
[
    'name' => 'Dinamik Kategori Menüsü',
    'slug' => 'dynamic_categories',
    'description' => 'Otomatik olarak kategorileri listeler',
    'html_template' => '@php
        $categories = App\\Models\\Category::where("status", true)
            ->limit($item->settings["limit"] ?? 10)
            ->orderBy("name")
            ->get();
    @endphp
    <div class="dropdown">
        <button class="dropdown-toggle">{{ $item->title }}</button>
        <div class="dropdown-menu">
            @foreach($categories as $category)
                <a href="{{ route("category.show", $category->slug) }}" class="dropdown-item">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>',
    'settings_schema' => [
        'limit' => [
            'type' => 'number',
            'default' => 10,
            'label' => 'Maksimum Kategori Sayısı'
        ],
        'order_by' => [
            'type' => 'select',
            'options' => ['name', 'created_at', 'popularity'],
            'default' => 'name',
            'label' => 'Sıralama Kriteri'
        ],
        'order_direction' => [
            'type' => 'select',
            'options' => ['asc', 'desc'],
            'default' => 'asc',
            'label' => 'Sıralama Yönü'
        ]
    ],
    'type' => 'dynamic'
]
```

### Şablon Seeder

```php
class MenuItemTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            // Yukarıdaki şablon tanımları...
        ];
        
        foreach ($templates as $template) {
            MenuItemTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
```

## 4. Çekici Sürükle-Bırak Arayüzü

### Blade Template

```blade
<div x-data="{ showEditor: false, currentItemId: null }">
    <div class="menu-builder-container">
        <div class="menu-builder-toolbar">
            <h2>{{ $menu->name }} Menüsü</h2>
            <button type="button" wire:click="addNewItem" class="btn-primary">
                <span class="icon"><i class="heroicon-o-plus"></i></span>
                <span>Yeni Öğe Ekle</span>
            </button>
        </div>
        
        <div class="menu-builder-content">
            <div class="menu-items-container">
                <div class="nested-sortable menu-items-list" id="menu-items-root">
                    @include('menu-manager::partials.menu-items-list', ['items' => $items])
                </div>
                
                @if(count($items) === 0)
                    <div class="empty-state">
                        <p>Bu menüde henüz öğe bulunmuyor.</p>
                        <button type="button" wire:click="addNewItem" class="btn-secondary">
                            İlk Menü Öğesini Ekle
                        </button>
                    </div>
                @endif
            </div>
            
            <div class="menu-item-editor" x-show="showEditor">
                @if($editingItem)
                    <div class="editor-header">
                        <h3>Menü Öğesi Düzenle</h3>
                        <button type="button" @click="showEditor = false" class="btn-close">
                            <span class="icon"><i class="heroicon-o-x-mark"></i></span>
                        </button>
                    </div>
                    
                    <div class="editor-body">
                        <div class="form-group">
                            <label for="item-title">Başlık</label>
                            <input type="text" id="item-title" wire:model="editingItem.title" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="item-type">Tip</label>
                            <select id="item-type" wire:model="editingItem.type" class="form-control">
                                <option value="link">Basit Link</option>
                                <option value="dropdown">Dropdown Menü</option>
                                <option value="mega_menu">Mega Menü</option>
                                <option value="html">HTML İçeriği</option>
                                <option value="dynamic">Dinamik İçerik</option>
                            </select>
                        </div>
                        
                        <div class="form-group" x-show="$wire.editingItem.type === 'link'">
                            <label for="item-url">URL</label>
                            <input type="text" id="item-url" wire:model="editingItem.url" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="item-template">Şablon</label>
                            <select id="item-template" wire:model="editingItem.template" class="form-control">
                                <option value="">Varsayılan</option>
                                @foreach($templates as $slug => $name)
                                    <option value="{{ $slug }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="item-icon">İkon</label>
                            <input type="text" id="item-icon" wire:model="editingItem.icon" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="item-target">Hedef</label>
                            <select id="item-target" wire:model="editingItem.target" class="form-control">
                                <option value="_self">Aynı Pencere</option>
                                <option value="_blank">Yeni Pencere</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="switch">
                                <input type="checkbox" wire:model="editingItem.status">
                                <span class="slider"></span>
                                <span class="label">Aktif</span>
                            </label>
                        </div>
                        
                        {{-- Şablon ayarları dinamik olarak yüklenir --}}
                        @if($editingItem->template)
                            @php
                                $template = $editingItem->getTemplateInstance();
                                $settingsSchema = $template ? $template->getSettingsFields() : [];
                            @endphp
                            
                            @if(count($settingsSchema) > 0)
                                <h4>Şablon Ayarları</h4>
                                
                                @foreach($settingsSchema as $key => $field)
                                    <div class="form-group">
                                        <label for="setting-{{ $key }}">{{ $field['label'] }}</label>
                                        
                                        @if($field['type'] === 'text' || $field['type'] === 'number')
                                            <input 
                                                type="{{ $field['type'] }}" 
                                                id="setting-{{ $key }}" 
                                                wire:model="editingItem.settings.{{ $key }}" 
                                                class="form-control"
                                                @if(isset($field['default'])) placeholder="{{ $field['default'] }}" @endif
                                            >
                                        @elseif($field['type'] === 'select')
                                            <select 
                                                id="setting-{{ $key }}" 
                                                wire:model="editingItem.settings.{{ $key }}" 
                                                class="form-control"
                                            >
                                                @foreach($field['options'] as $option)
                                                    <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field['type'] === 'boolean')
                                            <label class="switch">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="editingItem.settings.{{ $key }}"
                                                    @if($field['default']) checked @endif
                                                >
                                                <span class="slider"></span>
                                            </label>
                                        @elseif($field['type'] === 'textarea' || $field['type'] === 'code_editor')
                                            <textarea 
                                                id="setting-{{ $key }}" 
                                                wire:model="editingItem.settings.{{ $key }}" 
                                                class="form-control"
                                                rows="5"
                                            >{{ $field['default'] ?? '' }}</textarea>
                                        @elseif($field['type'] === 'color')
                                            <input 
                                                type="color" 
                                                id="setting-{{ $key }}" 
                                                wire:model="editingItem.settings.{{ $key }}" 
                                                class="form-control"
                                                value="{{ $field['default'] ?? '#ffffff' }}"
                                            >
                                        @endif
                                        
                                        @if(isset($field['description']))
                                            <small class="form-text text-muted">{{ $field['description'] }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                    
                    <div class="editor-footer">
                        <button type="button" wire:click="saveItem" class="btn-primary">Kaydet</button>
                        <button type="button" @click="showEditor = false" class="btn-secondary">İptal</button>
                        <button type="button" wire:click="deleteItem({{ $editingItem->id }})" class="btn-danger">Sil</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Menu Items List Partial --}}
@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        const nestedSortables = [].slice.call(document.querySelectorAll('.nested-sortable'));
        
        // Her bir sortable container için
        nestedSortables.forEach(sortable => {
            new Sortable(sortable, {
                group: 'nested-sortables',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                handle: '.drag-handle',
                onEnd: function(evt) {
                    // Menü öğelerinin yeni sıralamasını alma
                    const orderedItems = extractOrderFromDOM('menu-items-root');
                    @this.call('updateMenuOrder', orderedItems);
                }
            });
        });
        
        // DOM'dan sıralamayı çıkarma yardımcı fonksiyonu
        function extractOrderFromDOM(containerId) {
            const container = document.getElementById(containerId);
            return serializeNested(container);
        }
        
        // Nested listeyi seri hale getirme yardımcı fonksiyonu
        function serializeNested(parent) {
            const children = [].slice.call(parent.children);
            return children.map((child) => {
                const item = {
                    id: child.getAttribute('data-id')
                };
                
                const nestedContainer = child.querySelector('.nested-sortable');
                if (nestedContainer) {
                    item.children = serializeNested(nestedContainer);
                }
                
                return item;
            });
        }
        
        // Menü öğesi düzenleme işlevselliği
        window.addEventListener('edit-menu-item', event => {
            const itemId = event.detail.id;
            Alpine.store('menuEditor').currentItemId = itemId;
            Alpine.store('menuEditor').showEditor = true;
        });
        
        // Alpine.js store
        Alpine.store('menuEditor', {
            showEditor: false,
            currentItemId: null
        });
    });
</script>
@endpush

{{-- Menu Items List Partial Template --}}
<script type="text/template" id="menu-items-list-template">
    <ul class="nested-sortable">
        @foreach($items as $item)
            <li class="menu-item" data-id="{{ $item->id }}">
                <div class="menu-item-header">
                    <div class="drag-handle">
                        <i class="heroicon-o-bars-3"></i>
                    </div>
                    <div class="menu-item-title">
                        @if($item->icon)
                            <span class="menu-item-icon"><i class="{{ $item->icon }}"></i></span>
                        @endif
                        <span>{{ $item->title }}</span>
                    </div>
                    <div class="menu-item-type">
                        <span class="badge badge-{{ $item->type }}">{{ ucfirst($item->type) }}</span>
                    </div>
                    <div class="menu-item-status">
                        @if($item->status)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Pasif</span>
                        @endif
                    </div>
                    <div class="menu-item-actions">
                        <button type="button" wire:click="editItem({{ $item->id }})" class="btn-icon" @click="$store.menuEditor.showEditor = true; $store.menuEditor.currentItemId = {{ $item->id }}">
                            <i class="heroicon-o-pencil"></i>
                        </button>
                        <button type="button" wire:click="addChildItem({{ $item->id }})" class="btn-icon">
                            <i class="heroicon-o-plus"></i>
                        </button>
                        <button type="button" wire:click="deleteItem({{ $item->id }})" class="btn-icon text-danger" onclick="confirm('Bu menü öğesini silmek istediğinize emin misiniz?') || event.stopImmediatePropagation()">
                            <i class="heroicon-o-trash"></i>
                        </button>
                    </div>
                </div>
                
                @if(count($item->children) > 0)
                    @include('menu-manager::partials.menu-items-list', ['items' => $item->children])
                @endif
            </li>
        @endforeach
    </ul>
</script>
```

### JavaScript İle Sürükle-Bırak

```javascript
// Sortable.js kütüphanesi ile entegrasyon
import Sortable from 'sortablejs';

// Sürükle-bırak işlevselliği
function initSortable() {
    const nestedSortables = document.querySelectorAll('.nested-sortable');
    
    // Her bir sortable için
    nestedSortables.forEach(sortable => {
        new Sortable(sortable, {
            group: 'nested-sortables',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.drag-handle',
            onEnd: function(evt) {
                // Menü öğelerinin yeni sıralamasını al
                const orderedItems = extractOrderFromDOM();
                Livewire.emit('updateMenuOrder', orderedItems);
            }
        });
    });
}

// DOM'dan sıralamayı çıkarma
function extractOrderFromDOM() {
    const container = document.querySelector('#menu-items-root');
    return serializeNested(container);
}

// Nested yapıyı JSON'a çevirme
function serializeNested(parent) {
    const children = Array.from(parent.children);
    
    return children.map(child => {
        const item = {
            id: child.dataset.id
        };
        
        const nestedContainer = child.querySelector('.nested-sortable');
        if (nestedContainer && nestedContainer.children.length > 0) {
            item.children = serializeNested(nestedContainer);
        }
        
        return item;
    });
}

// Sayfa yüklendiğinde sortable'ı başlat
document.addEventListener('DOMContentLoaded', function() {
    initSortable();
    
    // Livewire sayfa güncellemelerinde sortable'ı yeniden başlat
    document.addEventListener('livewire:load', function() {
        initSortable();
    });
    
    // Editör genişliğini ayarla
    const editorToggle = document.querySelector('.editor-toggle');
    if (editorToggle) {
        editorToggle.addEventListener('click', function() {
            document.querySelector('.menu-builder-content').classList.toggle('show-editor');
        });
    }
});
```

## 5. Menü Render Motoru

### MenuRenderer Sınıfı

```php
class MenuRenderer
{
    protected $menu;
    protected $cacheEnabled = true;
    protected $cacheTime = 60; // dakika
    protected $menuFilter;
    
    public function __construct(Menu $menu, MenuFilter $menuFilter = null)
    {
        $this->menu = $menu;
        $this->menuFilter = $menuFilter ?: new MenuFilter();
    }
    
    public function disableCache()
    {
        $this->cacheEnabled = false;
        return $this;
    }
    
    public function setCacheTime($minutes)
    {
        $this->cacheTime = $minutes;
        return $this;
    }
    
    public function render($location = null)
    {
        $cacheKey = "menu_{$this->menu->id}_" . ($location ?? 'default');
        
        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $items = $this->getMenuItems($location);
        $html = $this->renderItems($items);
        
        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $html, now()->addMinutes($this->cacheTime));
            
            // Cache key'i kaydet
            app('menu.cache.manager')->registerCacheKey($cacheKey);
        }
        
        return $html;
    }
    
    protected function getMenuItems($location)
    {
        $query = $this->menu->items()->with('allChildren')->active();
        
        if ($location) {
            $query->where('location', $location);
        }
        
        $items = $query->orderBy('order')->get();
        
        // Erişim filtreleme
        return $this->menuFilter->filter($items);
    }
    
    protected function renderItems($items)
    {
        $html = '<ul class="menu menu-' . $this->menu->slug . '">';
        
        foreach ($items as $item) {
            $html .= $this->renderItem($item);
        }
        
        $html .= '</ul>';
        
        return $html;
    }
    
    protected function renderItem($item)
    {
        // Şablona göre render etme
        if ($item->template) {
            $template = MenuItemTemplate::where('slug', $item->template)->first();
            
            if ($template) {
                return $this->renderTemplate($template, $item);
            }
        }
        
        // Tip bazlı varsayılan render
        switch ($item->type) {
            case 'link':
                return $this->renderLink($item);
            case 'dropdown':
                return $this->renderDropdown($item);
            case 'mega_menu':
                return $this->renderMegaMenu($item);
            case 'html':
                return $this->renderHtml($item);
            case 'dynamic':
                return $this->renderDynamic($item);
            default:
                return $this->renderLink($item);
        }
    }
    
    protected function renderTemplate($template, $item)
    {
        try {
            return view('menu-manager::templates.custom', [
                'html' => Blade::render(
                    $template->html_template, 
                    ['item' => $item]
                )
            ])->render();
        } catch (\Exception $e) {
            return '<!-- Şablon render hatası: ' . $e->getMessage() . ' -->';
        }
    }
    
    protected function renderLink($item)
    {
        $html = '<li class="menu-item menu-item-' . $item->id . '">';
        $html .= '<a href="' . $item->url . '" target="' . $item->target . '">';
        
        if ($item->icon) {
            $html .= '<span class="menu-item-icon"><i class="' . $item->icon . '"></i></span>';
        }
        
        $html .= '<span class="menu-item-text">' . $item->title . '</span>';
        $html .= '</a>';
        $html .= '</li>';
        
        return $html;
    }
    
    protected function renderDropdown($item)
    {
        $html = '<li class="menu-item menu-item-dropdown menu-item-' . $item->id . '">';
        $html .= '<a href="' . ($item->url ?: '#') . '" class="dropdown-toggle" target="' . $item->target . '">';
        
        if ($item->icon) {
            $html .= '<span class="menu-item-icon"><i class="' . $item->icon . '"></i></span>';
        }
        
        $html .= '<span class="menu-item-text">' . $item->title . '</span>';
        $html .= '<span class="dropdown-indicator"><i class="fa fa-chevron-down"></i></span>';
        $html .= '</a>';
        
        if ($item->children->count() > 0) {
            $html .= '<ul class="dropdown-menu">';
            
            foreach ($item->children as $child) {
                $html .= $this->renderItem($child);
            }
            
            $html .= '</ul>';
        }
        
        $html .= '</li>';
        
        return $html;
    }
    
    protected function renderMegaMenu($item)
    {
        $settings = $item->settings ?: [];
        $columnCount = $settings['column_count'] ?? 3;
        
        $html = '<li class="menu-item menu-item-mega menu-item-' . $item->id . '">';
        $html .= '<a href="' . ($item->url ?: '#') . '" class="mega-menu-toggle" target="' . $item->target . '">';
        
        if ($item->icon) {
            $html .= '<span class="menu-item-icon"><i class="' . $item->icon . '"></i></span>';
        }
        
        $html .= '<span class="menu-item-text">' . $item->title . '</span>';
        $html .= '<span class="dropdown-indicator"><i class="fa fa-chevron-down"></i></span>';
        $html .= '</a>';
        
        if ($item->children->count() > 0) {
            $html .= '<div class="mega-menu-wrapper">';
            $html .= '<div class="mega-menu-container">';
            
            // Çocukları sütunlara böl
            $children = $item->children;
            $columns = $children->chunk(ceil($children->count() / $columnCount));
            
            $html .= '<div class="mega-menu-grid mega-menu-' . $columnCount . '-columns">';
            
            foreach ($columns as $columnItems) {
                $html .= '<div class="mega-menu-column">';
                
                foreach ($columnItems as $child) {
                    $html .= $this->renderItem($child);
                }
                
                $html .= '</div>';
            }
            
            // Öne çıkan içerik sütunu (eğer ayarlanmışsa)
            if (!empty($settings['show_featured_content']) && $settings['show_featured_content']) {
                $html .= '<div class="mega-menu-featured">';
                $html .= $this->renderFeaturedContent($item);
                $html .= '</div>';
            }
            
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</li>';
        
        return $html;
    }
    
    protected function renderHtml($item)
    {
        $settings = $item->settings ?: [];
        $html = '<li class="menu-item menu-item-html menu-item-' . $item->id . '">';
        $html .= $settings['html_content'] ?? '';
        $html .= '</li>';
        
        return $html;
    }
    
    protected function renderDynamic($item)
    {
        $settings = $item->settings ?: [];
        $dynamicType = $settings['dynamic_type'] ?? 'categories';
        
        // Basit dinamik renderlar için
        // Not: Gerçek uygulamada bu mantık daha gelişmiş olmalı
        switch ($dynamicType) {
            case 'categories':
                return $this->renderDynamicCategories($item);
            case 'recent_posts':
                return $this->renderDynamicRecentPosts($item);
            default:
                return '<!-- Bilinmeyen dinamik tip: ' . $dynamicType . ' -->';
        }
    }
    
    protected function renderFeaturedContent($item)
    {
        $settings = $item->settings ?: [];
        $contentType = $settings['featured_content_type'] ?? 'html';
        
        // Not: Gerçek uygulamada bu özelleştirilebilir olmalı
        $html = '<div class="mega-menu-featured-content">';
        
        switch ($contentType) {
            case 'html':
                $html .= $settings['featured_html_content'] ?? '';
                break;
            // Diğer içerik tipleri...
            default:
                $html .= '<!-- Bilinmeyen içerik tipi: ' . $contentType . ' -->';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    // Diğer dinamik render fonksiyonları...
}
```

### MenuFilter Sınıfı

```php
class MenuFilter
{
    public function filter($items)
    {
        return $items->filter(function ($item) {
            // İzinleri kontrol et
            if (!empty($item->permissions) && !$this->checkPermissions($item->permissions)) {
                return false;
            }
            
            // Özel koşulları kontrol et
            if (!empty($item->settings['conditions']) && !$this->evaluateConditions($item->settings['conditions'])) {
                return false;
            }
            
            // Alt öğeleri de filtrele
            if ($item->children->count() > 0) {
                $item->setRelation('children', $this->filter($item->children));
            }
            
            return true;
        });
    }
    
    protected function checkPermissions($permissions)
    {
        if (!auth()->check()) {
            return in_array('guest', $permissions);
        }
        
        $user = auth()->user();
        
        // Eğer herhangi bir izin verilmişse, hiç biri yoksa reddet
        if (in_array('authenticated', $permissions)) {
            return true;
        }
        
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function evaluateConditions($conditions)
    {
        // URL path kontrolü
        if (isset($conditions['url_path'])) {
            $currentPath = request()->path();
            $pattern = $conditions['url_path'];
            
            return Str::is($pattern, $currentPath);
        }
        
        // Sorgu parametreleri kontrolü
        if (isset($conditions['query_params'])) {
            foreach ($conditions['query_params'] as $param => $value) {
                if (request()->query($param) != $value) {
                    return false;
                }
            }
            
            return true;
        }
        
        // Tarih/zaman kontrolü
        if (isset($conditions['time_range'])) {
            $now = now();
            $start = isset($conditions['time_range']['start']) ? \Carbon\Carbon::parse($conditions['time_range']['start']) : null;
            $end = isset($conditions['time_range']['end']) ? \Carbon\Carbon::parse($conditions['time_range']['end']) : null;
            
            if ($start && $now->lt($start)) {
                return false;
            }
            
            if ($end && $now->gt($end)) {
                return false;
            }
            
            return true;
        }
        
        // Kullanıcı özellikleri kontrolü
        if (isset($conditions['user'])) {
            if (!auth()->check()) {
                return false;
            }
            
            $user = auth()->user();
            
            foreach ($conditions['user'] as $property => $value) {
                if ($user->{$property} != $value) {
                    return false;
                }
            }
            
            return true;
        }
        
        return true;
    }
}
```

## 6. Blade ve JS Bileşenleri

### Blade Component

```php
// MenuComponent.php
namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Menu;
use App\Services\MenuRenderer;

class MenuComponent extends Component
{
    public $menuId;
    public $location;
    public $class;
    
    public function __construct($menuId = null, $location = null, $class = null)
    {
        $this->menuId = $menuId;
        $this->location = $location;
        $this->class = $class;
    }
    
    public function render()
    {
        if ($this->menuId) {
            $menu = Menu::find($this->menuId);
        } else if ($this->location) {
            $menu = Menu::where('location', $this->location)
                ->where('status', true)
                ->first();
        } else {
            $menu = null;
        }
        
        if (!$menu) {
            return view('menu-manager::components.error', [
                'message' => 'Menü bulunamadı'
            ]);
        }
        
        $renderer = new MenuRenderer($menu);
        $html = $renderer->render();
        
        return view('menu-manager::components.menu', [
            'menu' => $menu,
            'html' => $html,
            'class' => $this->class
        ]);
    }
}
```

### Kullanım

```blade
{{-- Blade view içinde --}}
<x-menu menu-id="1" class="main-header-menu" />

{{-- Veya lokasyona göre --}}
<x-menu location="main-header" class="navbar-nav" />
```

### JS Bileşeni (Vue.js)

```vue
<!-- MegaMenu.vue -->
<template>
  <div class="mega-menu-component" :class="{'is-open': isOpen}">
    <button @click="toggleMenu" class="mega-menu-toggle">
      <span v-if="icon" class="menu-icon"><i :class="icon"></i></span>
      <span class="menu-title">{{ title }}</span>
      <span class="menu-indicator"><i class="fa" :class="isOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i></span>
    </button>
    
    <transition name="fade">
      <div v-if="isOpen" class="mega-menu-panel" :class="megaMenuClasses">
        <div class="mega-menu-container">
          <div class="mega-menu-columns" :class="'columns-' + columns.length">
            <div v-for="(column, index) in columns" :key="index" class="mega-menu-column">
              <h4 v-if="column.title" class="column-title">{{ column.title }}</h4>
              <ul class="column-items">
                <li v-for="item in column.items" :key="item.id" class="column-item">
                  <a :href="item.url" :target="item.target" class="column-link">
                    <span v-if="item.icon" class="item-icon"><i :class="item.icon"></i></span>
                    <span class="item-title">{{ item.title }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          
          <div v-if="featuredContent" class="mega-menu-featured">
            <div v-html="featuredContent"></div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    },
    icon: String,
    items: {
      type: Array,
      default: () => []
    },
    columnCount: {
      type: Number,
      default: 3
    },
    featuredContent: String,
    settings: {
      type: Object,
      default: () => ({})
    }
  },
  
  data() {
    return {
      isOpen: false,
      columns: []
    }
  },
  
  computed: {
    megaMenuClasses() {
      return {
        [`bg-${this.settings.backgroundColor || 'white'}`]: true,
        'has-featured-content': !!this.featuredContent
      }
    }
  },
  
  created() {
    this.organizeItemsIntoColumns();
  },
  
  methods: {
    toggleMenu() {
      this.isOpen = !this.isOpen;
    },
    
    organizeItemsIntoColumns() {
      const columnCount = this.columnCount;
      const itemsPerColumn = Math.ceil(this.items.length / columnCount);
      
      // Özel sütun düzeni varsa
      if (this.settings.columnLayout === 'manual' && this.settings.manualColumns) {
        this.columns = this.settings.manualColumns.map(column => ({
          title: column.title,
          items: this.items.filter(item => column.itemIds.includes(item.id))
        }));
        return;
      }
      
      // Eşit dağılım
      this.columns = [];
      for (let i = 0; i < columnCount; i++) {
        const startIdx = i * itemsPerColumn;
        const endIdx = Math.min(startIdx + itemsPerColumn, this.items.length);
        
        this.columns.push({
          title: '',
          items: this.items.slice(startIdx, endIdx)
        });
      }
    }
  }
}
</script>
```

## 7. Responsive Tasarım ve Mobil Menü

### Base CSS

```css
/* menu-base.css */

/* Base Menu Styles */
.menu {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-wrap: wrap;
}

.menu-horizontal {
  flex-direction: row;
}

.menu-vertical {
  flex-direction: column;
}

.menu-item {
  position: relative;
}

.menu-item a {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: var(--menu-text-color, #333);
  transition: all 0.2s ease;
}

.menu-item a:hover {
  background-color: var(--menu-hover-bg, rgba(0,0,0,0.05));
}

.menu-item-icon {
  margin-right: 0.5rem;
}

/* Dropdown Styles */
.menu-item-dropdown {
  position: relative;
}

.dropdown-menu {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  min-width: 200px;
  background-color: var(--dropdown-bg, white);
  border: 1px solid var(--dropdown-border-color, rgba(0,0,0,0.1));
  border-radius: 0.25rem;
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
  z-index: 1000;
  list-style: none;
  margin: 0;
  padding: 0.5rem 0;
}

.menu-item-dropdown:hover > .dropdown-menu,
.menu-item-dropdown:focus-within > .dropdown-menu {
  display: block;
}

.dropdown-menu .menu-item a {
  padding: 0.5rem 1rem;
  display: block;
  white-space: nowrap;
}

.dropdown-indicator {
  margin-left: 0.5rem;
  font-size: 0.75rem;
}

/* Mega Menu Styles */
.menu-item-mega {
  position: static;
}

.mega-menu-wrapper {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  background-color: var(--mega-menu-bg, white);
  border: 1px solid var(--mega-menu-border-color, rgba(0,0,0,0.1));
  border-top: none;
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
  z-index: 1000;
}

.menu-item-mega:hover .mega-menu-wrapper,
.menu-item-mega:focus-within .mega-menu-wrapper {
  display: block;
}

.mega-menu-container {
  width: 100%;
  max-width: var(--container-width, 1200px);
  margin: 0 auto;
  padding: 1.5rem;
}

.mega-menu-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.mega-menu-column {
  flex: 1;
  min-width: 200px;
}

.mega-menu-column h4 {
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--mega-menu-divider-color, rgba(0,0,0,0.1));
  font-size: 1rem;
  font-weight: 600;
}

.mega-menu-column ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.mega-menu-column li a {
  padding: 0.5rem 0;
  display: block;
  color: var(--mega-menu-link-color, #333);
  text-decoration: none;
  transition: all 0.2s ease;
}

.mega-menu-column li a:hover {
  color: var(--mega-menu-link-hover-color, #0066cc);
}

.mega-menu-featured {
  flex: 0 0 300px;
  padding-left: 1.5rem;
  margin-left: 1.5rem;
  border-left: 1px solid var(--mega-menu-divider-color, rgba(0,0,0,0.1));
}

/* Mobile Menu Styles */
.mobile-menu-toggle {
  display: none;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.5rem;
}

/* Responsive Breakpoints */
@media (max-width: 1024px) {
  .mega-menu-grid {
    flex-direction: column;
  }
  
  .mega-menu-featured {
    flex: 0 0 auto;
    width: 100%;
    padding-left: 0;
    margin-left: 0;
    border-left: none;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--mega-menu-divider-color, rgba(0,0,0,0.1));
  }
}

@media (max-width: 768px) {
  .mobile-menu-toggle {
    display: flex;
  }
  
  .menu-horizontal {
    display: none;
    flex-direction: column;
    width: 100%;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: var(--mobile-menu-bg, white);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    z-index: 1000;
  }
  
  .menu-horizontal.mobile-menu-open {
    display: flex;
  }
  
  .menu-item {
    width: 100%;
  }
  
  .dropdown-menu {
    position: static;
    display: none;
    box-shadow: none;
    border: none;
    border-radius: 0;
    background-color: var(--mobile-dropdown-bg, rgba(0,0,0,0.02));
    padding: 0;
    width: 100%;
  }
  
  .menu-item-dropdown.mobile-active > .dropdown-menu {
    display: block;
  }
  
  .mega-menu-wrapper {
    position: static;
    box-shadow: none;
    border: none;
  }
  
  .mega-menu-container {
    padding: 0.5rem;
  }
}
```

### Mobile Menu JavaScript

```javascript
// mobile-menu.js

// Mobil menü işlevselliği
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const menu = document.querySelector('.menu-horizontal');
    
    if (mobileToggle && menu) {
        mobileToggle.addEventListener('click', function() {
            menu.classList.toggle('mobile-menu-open');
            this.setAttribute('aria-expanded', 
                this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true'
            );
        });
    }
    
    // Mobil dropdown açma/kapama
    const dropdownToggles = document.querySelectorAll('.menu-item-dropdown > a');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            // Sadece mobil görünümde aktif
            if (window.innerWidth <= 768) {
                e.preventDefault();
                
                const parentLi = this.parentElement;
                parentLi.classList.toggle('mobile-active');
                
                const isExpanded = parentLi.classList.contains('mobile-active');
                this.setAttribute('aria-expanded', isExpanded);
            }
        });
    });
    
    // Mobil menü dışına tıklandığında kapat
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            const clickedOnMenu = menu.contains(e.target) || mobileToggle.contains(e.target);
            
            if (!clickedOnMenu && menu.classList.contains('mobile-menu-open')) {
                menu.classList.remove('mobile-menu-open');
                mobileToggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
    
    // Pencere boyutu değiştiğinde menü durumunu sıfırla
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            if (menu) {
                menu.classList.remove('mobile-menu-open');
            }
            
            document.querySelectorAll('.menu-item-dropdown').forEach(item => {
                item.classList.remove('mobile-active');
            });
            
            document.querySelectorAll('.menu-item-dropdown > a').forEach(item => {
                item.setAttribute('aria-expanded', 'false');
            });
        }
    });
});
```

## 8. Cache Stratejisi ve Performans

### MenuCacheManager

```php
class MenuCacheManager
{
    protected $cachePrefix = 'menu_';
    protected $cacheKeysKey = 'menu_cache_keys';
    protected $defaultCacheDuration = 60; // dakika
    
    public function clearMenuCache($menuId = null)
    {
        if ($menuId) {
            $this->clearMenuById($menuId);
        } else {
            $this->clearAllMenus();
        }
    }
    
    protected function clearMenuById($menuId)
    {
        // Temel anahtarları temizle
        Cache::forget("{$this->cachePrefix}{$menuId}_default");
        
        // Lokasyonlara göre önbellekleri temizle
        $locations = Menu::where('id', $menuId)->value('location');
        if ($locations) {
            foreach (explode(',', $locations) as $location) {
                Cache::forget("{$this->cachePrefix}{$menuId}_{$location}");
            }
        }
        
        // Muhtemelen üretilmiş diğer anahtarları bul ve temizle
        $cacheKeys = Cache::get($this->cacheKeysKey, []);
        $keysToRemove = [];
        
        foreach ($cacheKeys as $key) {
            if (Str::startsWith($key, "{$this->cachePrefix}{$menuId}_")) {
                Cache::forget($key);
                $keysToRemove[] = $key;
            }
        }
        
        // Anahtarlar listesini güncelle
        if (!empty($keysToRemove)) {
            $updatedKeys = array_diff($cacheKeys, $keysToRemove);
            Cache::put($this->cacheKeysKey, $updatedKeys, now()->addDays(30));
        }
    }
    
    protected function clearAllMenus()
    {
        // Tüm menü önbelleklerini temizle
        $cacheKeys = Cache::get($this->cacheKeysKey, []);
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        Cache::forget($this->cacheKeysKey);
    }
    
    public function registerCacheKey($key)
    {
        $cacheKeys = Cache::get($this->cacheKeysKey, []);
        
        if (!in_array($key, $cacheKeys)) {
            $cacheKeys[] = $key;
            Cache::put($this->cacheKeysKey, $cacheKeys, now()->addDays(30));
        }
    }
    
    public function getCacheDuration($menuId = null)
    {
        if ($menuId) {
            $duration = Menu::where('id', $menuId)->value('cache_duration');
            return $duration ?: $this->defaultCacheDuration;
        }
        
        return $this->defaultCacheDuration;
    }
    
    public function warmupCache()
    {
        // Önbellekleri ısıtma (örn. queue job ile çalıştırılabilir)
        $menus = Menu::where('status', true)->get();
        
        foreach ($menus as $menu) {
            $renderer = new MenuRenderer($menu);
            
            // Ana render
            $renderer->render();
            
            // Lokasyon bazlı renderlar
            if ($menu->location) {
                foreach (explode(',', $menu->location) as $location) {
                    $renderer->render($location);
                }
            }
        }
    }
}
```

### Cache Event Listeners

```php
class MenuEventSubscriber
{
    protected $cacheManager;
    
    public function __construct(MenuCacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }
    
    public function handleMenuSaved(Menu $menu)
    {
        $this->cacheManager->clearMenuCache($menu->id);
    }
    
    public function handleMenuDeleted(Menu $menu)
    {
        $this->cacheManager->clearMenuCache($menu->id);
    }
    
    public function handleMenuItemSaved(MenuItem $item)
    {
        $this->cacheManager->clearMenuCache($item->menu_id);
    }
    
    public function handleMenuItemDeleted(MenuItem $item)
    {
        $this->cacheManager->clearMenuCache($item->menu_id);
    }
    
    public function subscribe($events)
    {
        $events->listen(
            'eloquent.saved: ' . Menu::class,
            [MenuEventSubscriber::class, 'handleMenuSaved']
        );
        
        $events->listen(
            'eloquent.deleted: ' . Menu::class,
            [MenuEventSubscriber::class, 'handleMenuDeleted']
        );
        
        $events->listen(
            'eloquent.saved: ' . MenuItem::class,
            [MenuEventSubscriber::class, 'handleMenuItemSaved']
        );
        
        $events->listen(
            'eloquent.deleted: ' . MenuItem::class,
            [MenuEventSubscriber::class, 'handleMenuItemDeleted']
        );
    }
}
```

## Aşamalı Geliştirme Planı

### Aşama 1: Temel Veri Modeli ve Admin Arayüzü (Hafta 1-2)
- [x] Veritabanı tabloları ve ilişkileri oluşturma
- [x] Temel modelleri geliştirme (Menu, MenuItem)
- [x] Filament Resource sınıflarını oluşturma
- [x] Basit CRUD işlemleri için admin arayüzü

### Aşama 2: Sürükle-Bırak Sıralama ve Hiyerarşi (Hafta 2-3)
- [ ] Sürükle-bırak menü düzenleyici geliştirme
- [ ] JavaScript entegrasyonu (Sortable.js)
- [ ] Menü öğelerini kaydetme/güncelleme mantığı
- [ ] Hiyerarşik menü yapısı desteği

### Aşama 3: Şablonlar ve Özelleştirme Seçenekleri (Hafta 3-4)
- [ ] MenuItemTemplate modelini geliştirme
- [ ] Farklı menü tiplerini destekleyen şablonlar oluşturma
- [ ] Şablon seeder ve veritabanı kayıtları
- [ ] Özelleştirme ayarlarını UI'a entegre etme

### Aşama 4: Render Motoru ve Blade Bileşenleri (Hafta 4-5)
- [ ] MenuRenderer sınıfını geliştirme
- [ ] Blade bileşenlerini oluşturma
- [ ] Tema entegrasyonu
- [ ] Önbellekleme stratejisi

### Aşama 5: Mega Menüler ve Gelişmiş Özellikler (Hafta 5-6)
- [ ] Mega menü tasarımı ve düzeni
- [ ] Mega menü şablonlarını geliştirme
- [ ] Dinamik içerik entegrasyonu
- [ ] JavaScript etkileşimleri

### Aşama 6: Mobil Menü ve Responsive Tasarım (Hafta 6-7)
- [ ] Mobil menü CSS yapısı
- [ ] Mobil menü JavaScript davranışları
- [ ] Responsive breakpoint'leri
- [ ] Kullanılabilirlik testleri

### Aşama 7: Tamamlama ve Optimizasyon (Hafta 7-8)
- [ ] Performans optimizasyonu
- [ ] Erişilebilirlik (ARIA) iyileştirmeleri
- [ ] Dokümantasyon
- [ ] Son testler ve düzeltmeler

## Kod ve Tasarım İlkeleri

1. **Esneklik**: Farklı menü tipleri ve şablonlar için esnek bir yapı
2. **Performans**: Verimli önbellekleme ve render etme stratejisi
3. **UX Odaklı**: Admin panelinde kolay kullanım, sezgisel arayüz
4. **Genişletilebilirlik**: Yeni menü tipleri ve şablonlar ekleyebilme
5. **Erişilebilirlik**: ARIA uyumlu, klavye navigasyonu destekleyen menüler
6. **Responsive**: Tüm cihazlarda düzgün çalışma
7. **Modülerlik**: Kod tabanının organize ve sürdürülebilir olması
8. **Bellek Verimliliği**: Büyük menüler için optimize edilmiş veri yapıları