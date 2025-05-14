<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-6">XML/JSON Yapı Analizi ve Haritalama Aracı</h1>
                    
                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-800 text-red-800 dark:text-red-200 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-400 mb-2">
                            Bu araç, dış sistemlerden gelen XML veya JSON verilerini analiz etmenize ve uygun alanları haritalandırmanıza yardımcı olur.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div x-data="{ 
                                format: 'xml',
                                loading: false,
                                inputData: '',
                                error: null,
                                sampleXml: `<?xml version="1.0" encoding="UTF-8"?>
<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0">
  <POS>
    <Source>
      <RequestorID ID="SUPPLIER" Type="1"/>
    </Source>
  </POS>
  <AvailRequestSegments>
    <AvailRequestSegment>
      <HotelSearchCriteria>
        <Criterion>
          <HotelRef HotelCode="12345"/>
          <StayDateRange Start="2025-06-01" End="2025-06-05"/>
          <RoomStayCandidates>
            <RoomStayCandidate Quantity="1">
              <GuestCounts>
                <GuestCount AgeQualifyingCode="10" Count="2"/>
                <GuestCount AgeQualifyingCode="8" Count="1"/>
              </GuestCounts>
            </RoomStayCandidate>
          </RoomStayCandidates>
        </Criterion>
      </HotelSearchCriteria>
    </AvailRequestSegment>
  </AvailRequestSegments>
</OTA_HotelAvailRQ>`,
                                sampleJson: `{
  "hotel": {
    "id": "12345",
    "name": "Grand Hotel",
    "rooms": [
      {
        "id": "101",
        "type": "standard",
        "availability": [
          {
            "date": "2025-06-01",
            "count": 5,
            "price": 120.50
          },
          {
            "date": "2025-06-02",
            "count": 3,
            "price": 120.50
          }
        ]
      }
    ]
  }
}`,
                                loadSample() {
                                    this.inputData = this.format === 'xml' ? this.sampleXml : this.sampleJson;
                                },
                                analyzeData() {
                                    if (!this.inputData.trim()) {
                                        this.error = 'Lütfen analiz edilecek veri girin';
                                        return;
                                    }
                                    
                                    // Form manuel olarak gönderilir
                                    document.getElementById('analyzer-form').submit();
                                    
                                    this.loading = true;
                                    this.error = null;
                                }
                            }" class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg shadow-sm">
                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold mb-2">Veri Formatı</h2>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="format" value="xml" x-model="format">
                                            <span class="ml-2">XML</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="format" value="json" x-model="format">
                                            <span class="ml-2">JSON</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold mb-2">Kaynak Veri</h2>
                                    <div class="mb-2 flex space-x-2">
                                        <button @click="loadSample" class="px-2 py-1 text-xs text-white bg-blue-500 hover:bg-blue-600 rounded">
                                            Örnek Veri Yükle
                                        </button>
                                        <button @click="inputData = ''" class="px-2 py-1 text-xs text-white bg-gray-500 hover:bg-gray-600 rounded">
                                            Temizle
                                        </button>
                                    </div>
                                    <textarea
                                        x-model="inputData"
                                        class="w-full h-96 p-2 border border-gray-300 dark:border-gray-700 rounded font-mono text-sm"
                                        :placeholder="format === 'xml' ? 'Analiz edilecek XML verisi...' : 'Analiz edilecek JSON verisi...'"
                                    ></textarea>
                                </div>
                                
                                <div>
                                    <form id="analyzer-form" method="POST" action="{{ route('xml-mapper.analyze') }}" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="data" :value="inputData">
                                        <input type="hidden" name="format" :value="format">
                                    </form>
                                    
                                    <button 
                                        @click="analyzeData"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded"
                                        :disabled="loading"
                                    >
                                        <span x-show="!loading">Analiz Et</span>
                                        <span x-show="loading">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Analiz ediliyor...
                                        </span>
                                    </button>
                                </div>
                                
                                <div x-show="error" class="mt-4 p-3 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 rounded-md">
                                    <span x-text="error"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div 
                                x-data="{ 
                                    paths: [],
                                    selectedPaths: {},
                                    targetMappings: {},
                                    mappingResult: {},
                                    copySuccess: false,
                                    
                                    togglePath(path) {
                                        this.selectedPaths[path] = !this.selectedPaths[path];
                                        if (!this.selectedPaths[path]) {
                                            delete this.targetMappings[path];
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
                                }"
                                @data-analyzed.window="
                                    paths = $event.detail.paths;
                                    selectedPaths = {};
                                    targetMappings = {};
                                    mappingResult = {};
                                "
                                class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg shadow-sm h-full"
                            >
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
                                
                                <div class="mb-4 overflow-y-auto max-h-80">
                                    <table class="min-w-full border border-gray-300 dark:border-gray-700">
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
                                
                                <div>
                                    <h3 class="text-md font-semibold mb-2">Eşleştirme Sonucu:</h3>
                                    <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-md mb-3 relative">
                                        <pre x-text="JSON.stringify(mappingResult, null, 2)" class="text-xs font-mono overflow-auto max-h-36"></pre>
                                        
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>