<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between mb-6">
                        <h1 class="text-2xl font-bold">XML/JSON Analiz Sonuçları</h1>
                        <a href="{{ route('xml-mapper.index') }}" class="inline-flex items-center px-3 py-2 text-sm leading-4 text-white bg-blue-600 hover:bg-blue-700 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                            </svg>
                            Yeni Analiz
                        </a>
                    </div>
                    
                    <div x-data="{
                        paths: {{ json_encode(array_values(array_filter($paths, fn($p) => !empty(trim($p))))) }},
                        selectedPaths: {},
                        targetMappings: {},
                        mappingResult: {},
                        copySuccess: false,
                        
                        togglePath(path) {
                            this.selectedPaths[path] = !this.selectedPaths[path];
                            if (!this.selectedPaths[path]) {
                                delete this.targetMappings[path];
                                this.generateMapping();
                            }
                        },
                        
                        updateTargetField(path, value) {
                            this.targetMappings[path] = value;
                            this.generateMapping();
                        },
                        
                        generateMapping() {
                            const result = {};
                            for (const [path, target] of Object.entries(this.targetMappings)) {
                                if (target && target.trim() !== '') {
                                    result[path] = target;
                                }
                            }
                            this.mappingResult = result;
                        },
                        
                        copyMapping() {
                            const textarea = document.createElement('textarea');
                            textarea.value = JSON.stringify(this.mappingResult, null, 2);
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textarea);
                            
                            this.copySuccess = true;
                            setTimeout(() => {
                                this.copySuccess = false;
                            }, 2000);
                        },
                        
                        selectAll() {
                            this.paths.forEach(path => {
                                this.selectedPaths[path] = true;
                            });
                        },
                        
                        clearAll() {
                            this.selectedPaths = {};
                            this.targetMappings = {};
                            this.mappingResult = {};
                        }
                    }" class="mb-6">
                        <div class="mb-4">
                            <h2 class="text-lg font-semibold">Tespit Edilen Alanlar</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                Eşleştirmek istediğiniz alanları seçin ve hedef alan adlarını girin.
                            </p>
                            
                            <div class="mb-2 flex justify-between">
                                <div>
                                    <button @click="selectAll" class="px-2 py-1 text-xs text-white bg-blue-500 hover:bg-blue-600 rounded mr-2">
                                        Tümünü Seç
                                    </button>
                                    <button @click="clearAll" class="px-2 py-1 text-xs text-white bg-gray-500 hover:bg-gray-600 rounded">
                                        Temizle
                                    </button>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        <span x-text="Object.values(selectedPaths).filter(v => v).length"></span> / <span x-text="paths.length"></span> alan seçildi
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
                            <div class="lg:col-span-4 overflow-y-auto max-h-[60vh]">
                                <div class="border border-gray-300 dark:border-gray-700 rounded">
                                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                        <thead class="bg-gray-100 dark:bg-gray-800">
                                            <tr>
                                                <th class="py-2 px-3 text-left text-xs font-medium uppercase tracking-wider">
                                                    Seç
                                                </th>
                                                <th class="py-2 px-3 text-left text-xs font-medium uppercase tracking-wider">
                                                    XML/JSON Yolu
                                                </th>
                                                <th class="py-2 px-3 text-left text-xs font-medium uppercase tracking-wider">
                                                    Hedef Alan
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                                            <template x-for="path in paths" :key="path">
                                                <tr>
                                                    <td class="py-2 px-3">
                                                        <input 
                                                            type="checkbox" 
                                                            :checked="selectedPaths[path]"
                                                            @click="togglePath(path)"
                                                        >
                                                    </td>
                                                    <td class="py-2 px-3 font-mono text-xs break-all">
                                                        <span x-text="path"></span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <input
                                                            x-show="selectedPaths[path]"
                                                            type="text"
                                                            class="w-full border border-gray-300 dark:border-gray-700 p-1 text-sm rounded"
                                                            :placeholder="path.split('.').pop()"
                                                            :value="targetMappings[path] || ''"
                                                            @input="updateTargetField(path, $event.target.value)"
                                                        >
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="lg:col-span-2">
                                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded border border-gray-300 dark:border-gray-700 sticky top-4">
                                    <h3 class="text-md font-semibold mb-2">Eşleştirme Sonucu:</h3>
                                    <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-md mb-3 relative">
                                        <pre x-text="JSON.stringify(mappingResult, null, 2)" class="text-xs font-mono overflow-auto max-h-80"></pre>
                                        
                                        <button 
                                            @click="copyMapping"
                                            class="absolute top-2 right-2 p-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded"
                                            title="Panoya Kopyala"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                            </svg>
                                        </button>
                                        
                                        <div
                                            x-show="copySuccess"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="absolute top-2 right-8 bg-green-500 text-white text-xs py-1 px-2 rounded"
                                        >
                                            Kopyalandı!
                                        </div>
                                    </div>
                                    
                                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-4">
                                        <p>Bu eşleştirmeyi API Mapping içinde kullanabilirsiniz.</p>
                                        
                                        <div class="flex mt-2">
                                            <a href="{{ route('filament.admin.resources.api-mappings.create') }}" 
                                               x-data=""
                                               x-on:click="
                                                   localStorage.setItem('xml_mapper_field_mappings', JSON.stringify(mappingResult));
                                                   $dispatch('notify', {
                                                       message: 'Eşleştirme verileri kopyalandı. API Mapping oluşturma formunda kullanabilirsiniz.'
                                                   })
                                               "
                                               class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-white bg-green-600 hover:bg-green-700 rounded-md"
                                               target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Yeni API Mapping Oluştur
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold mb-4">Veri Yapısı</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-md font-semibold mb-2">Kaynak {{ strtoupper($format) }} Verisi</h3>
                                <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-md overflow-auto max-h-96 font-mono text-xs">
                                    @if($format === 'xml')
                                        <pre class="whitespace-pre-wrap">{{ htmlspecialchars($sourceData) }}</pre>
                                    @else
                                        <pre class="whitespace-pre-wrap">{{ json_encode(json_decode($sourceData), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h3 class="text-md font-semibold mb-2">Çözümlenmiş Yapı</h3>
                                <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-md overflow-auto max-h-96 font-mono text-xs">
                                    <pre class="whitespace-pre-wrap">{{ json_encode($structure, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>