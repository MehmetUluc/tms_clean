<div class="menu-builder-container" x-data="menuBuilder">
    <div class="p-4 bg-white rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Menu Builder: {{ $menu->name }}</h2>
            <div class="space-x-2">
                <button type="button" @click="saveOrder" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    <span class="flex items-center">
                        <x-heroicon-o-save class="w-5 h-5 mr-1" />
                        Save Order
                    </span>
                </button>
                <button type="button" @click="$dispatch('open-modal', { id: 'add-menu-item-modal' })" class="px-4 py-2 bg-success-600 text-white rounded-lg hover:bg-success-700 transition">
                    <span class="flex items-center">
                        <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                        Add Item
                    </span>
                </button>
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <div class="text-sm text-gray-600 mb-2">
                <x-heroicon-o-information-circle class="w-5 h-5 inline-block mr-1" />
                Drag and drop items to reorder them. You can also drag items to make them children of other items.
            </div>
        </div>
        
        <div class="menu-items-container" id="menu-items-container">
            <!-- Root level items container -->
            <div class="menu-items-list" data-parent-id="null">
                @if(count($menuItems) > 0)
                    @foreach($menuItems as $item)
                        @if(empty($item['parent_id']))
                            <x-menu-manager::menu-item :item="$item" />
                        @endif
                    @endforeach
                @else
                    <div class="p-8 text-center text-gray-500 bg-gray-100 rounded-lg">
                        <p>No menu items yet. Click "Add Item" to create your first menu item.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('menuBuilder', () => ({
                menuItems: @json($menuItems),
                ordered: {},
                
                init() {
                    this.$nextTick(() => this.initSortable());
                },
                
                initSortable() {
                    // Initialize sortable on the root level
                    this.createSortable(document.querySelector('.menu-items-list[data-parent-id="null"]'));
                    
                    // Initialize sortable on any child lists
                    document.querySelectorAll('.menu-item-children').forEach(el => {
                        this.createSortable(el);
                    });
                },
                
                createSortable(el) {
                    if (!el) return;
                    
                    new Sortable(el, {
                        group: 'menu-items',
                        animation: 150,
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        handle: '.drag-handle',
                        onEnd: (evt) => {
                            this.updateOrder();
                        }
                    });
                },
                
                updateOrder() {
                    this.ordered = {};
                    
                    // Process the root level
                    this.processLevel(document.querySelector('.menu-items-list[data-parent-id="null"]'), null);
                    
                    // Process all child levels
                    document.querySelectorAll('.menu-item-children').forEach(el => {
                        const parentId = el.dataset.parentId;
                        if (parentId) {
                            this.processLevel(el, parseInt(parentId));
                        }
                    });
                },
                
                processLevel(container, parentId) {
                    if (!container) return;
                    
                    Array.from(container.children).forEach((item, index) => {
                        const itemId = item.dataset.itemId;
                        
                        if (itemId) {
                            this.ordered[itemId] = {
                                parent_id: parentId,
                                order: index
                            };
                        }
                    });
                },
                
                saveOrder() {
                    Livewire.dispatch('update-item-order', { itemsData: this.ordered });
                },
                
                deleteItem(itemId) {
                    if (confirm('Are you sure you want to delete this menu item? Child items will be moved up a level.')) {
                        Livewire.dispatch('delete-menu-item', { itemId });
                    }
                },
                
                toggleChildren(itemId) {
                    const containerEl = document.getElementById(`children-container-${itemId}`);
                    const buttonEl = document.getElementById(`toggle-button-${itemId}`);
                    
                    if (containerEl.classList.contains('hidden')) {
                        containerEl.classList.remove('hidden');
                        buttonEl.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                    } else {
                        containerEl.classList.add('hidden');
                        buttonEl.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
                    }
                }
            }));
        });
    </script>
    
    <style>
        .menu-item {
            transition: all 0.2s ease;
        }
        
        .sortable-ghost {
            opacity: 0.4;
            background: #e0e7ff !important;
        }
        
        .sortable-chosen {
            background: #f3f4f6;
        }
        
        .sortable-drag {
            background: #ffffff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .drag-handle {
            cursor: move;
        }
        
        .menu-item-children {
            padding-left: 2rem;
            margin-top: 0.5rem;
        }
    </style>
</div>