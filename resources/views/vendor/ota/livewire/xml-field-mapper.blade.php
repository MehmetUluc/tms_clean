<div class="p-4 bg-white rounded-lg shadow">
    <div class="grid grid-cols-2 gap-6">
        <!-- XML Alanları (Sol Sütun) -->
        <div class="bg-gray-50 p-4 rounded">
            <h3 class="text-lg font-medium mb-4">XML Alanları</h3>
            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                @foreach($xmlPaths as $path)
                <div class="flex items-center p-2 border rounded bg-white hover:bg-blue-50 cursor-move" 
                     data-path="{{ $path['path'] }}" 
                     data-type="{{ $path['type'] }}" 
                     draggable="true" 
                     wire:key="xml-{{ $path['path'] }}">
                    <div class="flex-1">
                        <div class="font-medium">{{ $path['path'] }}</div>
                        @if(isset($path['example']) && $path['example'])
                            <div class="text-xs text-gray-500">Örnek: {{ $path['example'] }}</div>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">{{ ucfirst($path['type']) }}</div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Sistem Alanları (Sağ Sütun) -->
        <div class="bg-gray-50 p-4 rounded">
            <h3 class="text-lg font-medium mb-4">Sistem Alanları</h3>
            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                @foreach($systemFields as $field => $details)
                <div class="flex items-center p-2 border rounded bg-white hover:bg-green-50 cursor-pointer" 
                     data-field="{{ $field }}" 
                     data-type="{{ $details['type'] }}" 
                     wire:key="sys-{{ $field }}">
                    <div class="flex-1">
                        <div class="font-medium">{{ $field }}</div>
                        <div class="text-xs text-gray-500">{{ $details['description'] }}</div>
                    </div>
                    <div class="text-sm text-gray-500">{{ ucfirst($details['type']) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Eşleştirme Alanı -->
    <div class="mt-8">
        <h3 class="text-lg font-medium mb-4">Eşleştirmeler</h3>
        <div class="bg-white border rounded-lg p-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">XML Alanı</th>
                        <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sistem Alanı</th>
                        <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dönüşüm</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="mappings-container">
                    <!-- Eşleştirmeler buraya eklenecek -->
                    @foreach($mappings as $index => $mapping)
                    <tr wire:key="mapping-{{ $index }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mapping['xmlPath'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">&rarr;</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mapping['systemField'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($mapping['hasTransformation'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($mapping['transformationType']) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Standart
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="text-indigo-600 hover:text-indigo-900" wire:click="editMapping({{ $index }})">
                                Düzenle
                            </button>
                            <button type="button" class="ml-4 text-red-600 hover:text-red-900" wire:click="removeMapping({{ $index }})">
                                Sil
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Eşleştirme yok ise mesaj göster -->
            @if(empty($mappings))
            <div class="text-center py-8 text-gray-500">
                Henüz eşleştirme eklenmedi. Alanları sürükleyerek veya yukarıdaki "Otomatik Eşleştir" düğmesini kullanarak eşleştirme yapabilirsiniz.
            </div>
            @endif
        </div>
    </div>
    
    <!-- Eşleştirme Düğmeleri -->
    <div class="mt-6 flex justify-between">
        <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click="autoMapFields">
            Otomatik Eşleştir
        </button>
        
        <div>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2" wire:click="resetMappings">
                Sıfırla
            </button>
            
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" wire:click="saveMappings">
                Eşleştirmeleri Kaydet
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function() {
        // Drag and drop işlevselliği
        const xmlItems = document.querySelectorAll('[data-path]');
        const systemItems = document.querySelectorAll('[data-field]');
        const mappingsContainer = document.getElementById('mappings-container');
        
        let draggedItem = null;
        
        // XML alanları için drag başlatma
        xmlItems.forEach(item => {
            item.addEventListener('dragstart', function(e) {
                draggedItem = {
                    type: 'xml',
                    path: this.getAttribute('data-path'),
                    element: this
                };
                this.classList.add('bg-blue-100');
                e.dataTransfer.setData('text/plain', this.getAttribute('data-path'));
            });
            
            item.addEventListener('dragend', function() {
                this.classList.remove('bg-blue-100');
                draggedItem = null;
            });
        });
        
        // Sistem alanları için drop event
        systemItems.forEach(item => {
            item.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('bg-green-100');
            });
            
            item.addEventListener('dragleave', function() {
                this.classList.remove('bg-green-100');
            });
            
            item.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('bg-green-100');
                
                if (draggedItem && draggedItem.type === 'xml') {
                    const xmlPath = draggedItem.path;
                    const systemField = this.getAttribute('data-field');
                    
                    @this.call('addMapping', xmlPath, systemField);
                }
            });
        });
    });
</script>