<x-filament::page>
    <div>
        {{ $this->form }}
    </div>
    
    <!-- Eşleştirme Modali -->
    <div x-data="{ 
        open: false, 
        xmlPath: '',
        hasTransformation: false,
        transformationType: 'none',
        systemField: '',
        dateFormat: '',
        currencyFrom: '',
        currencyTo: 'TRY',
        booleanFormat: '',
        fixedValue: '',
        customCode: '',
        
        init() {
            window.addEventListener('open-mapping-modal', event => {
                this.xmlPath = event.detail.xmlPath;
                this.open = true;
                this.resetForm();
                
                // Mevcut eşleştirmeyi kontrol et
                const mapping = @this.mappingData[this.xmlPath];
                if (mapping) {
                    if (typeof mapping === 'string') {
                        // Basit eşleştirme
                        this.systemField = mapping;
                        this.hasTransformation = false;
                    } else if (typeof mapping === 'object') {
                        // Dönüşüm içeren eşleştirme
                        this.systemField = mapping.target || '';
                        this.hasTransformation = true;
                        
                        if (mapping.format) {
                            this.transformationType = 'date_format';
                            this.dateFormat = mapping.format;
                        } else if (mapping.currency) {
                            this.transformationType = 'currency';
                            this.currencyFrom = mapping.currency.from || '';
                            this.currencyTo = mapping.currency.to || 'TRY';
                        } else if (mapping.boolean_format) {
                            this.transformationType = 'boolean';
                            this.booleanFormat = mapping.boolean_format;
                        } else if (mapping.fixed !== undefined) {
                            this.transformationType = 'fixed';
                            this.fixedValue = mapping.fixed;
                        } else if (mapping.custom) {
                            this.transformationType = 'custom';
                            this.customCode = mapping.custom;
                        }
                    }
                }
            });
        },
        
        resetForm() {
            this.systemField = '';
            this.hasTransformation = false;
            this.transformationType = 'none';
            this.dateFormat = '';
            this.currencyFrom = '';
            this.currencyTo = 'TRY';
            this.booleanFormat = '';
            this.fixedValue = '';
            this.customCode = '';
        },
        
        save() {
            let transformationOptions = null;
            
            if (this.hasTransformation) {
                transformationOptions = {};
                
                switch (this.transformationType) {
                    case 'date_format':
                        transformationOptions.format = this.dateFormat;
                        break;
                    case 'currency':
                        transformationOptions.currency = {
                            from: this.currencyFrom,
                            to: this.currencyTo
                        };
                        break;
                    case 'boolean':
                        transformationOptions.boolean_format = this.booleanFormat;
                        break;
                    case 'fixed':
                        transformationOptions.fixed = this.fixedValue;
                        break;
                    case 'custom':
                        transformationOptions.custom = this.customCode;
                        break;
                }
            }
            
            @this.saveFieldMapping(this.xmlPath, this.systemField, transformationOptions);
            this.open = false;
        }
    }" 
    x-show="open" 
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            
            <div class="relative bg-white rounded-lg w-full max-w-2xl mx-auto shadow-xl" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Alan Eşleştirme</h3>
                    <p class="mt-1 text-sm text-gray-500">XML alanını sistem alanıyla eşleştirin</p>
                </div>
                
                <div class="border-t border-gray-200">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">XML Alanı</label>
                            <div class="mt-1 p-2 bg-gray-50 rounded border" x-text="xmlPath"></div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="systemField" class="block text-sm font-medium text-gray-700">Sistem Alanı</label>
                            <select id="systemField" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" x-model="systemField">
                                <option value="">-- Sistem alanı seçin --</option>
                                @foreach($this->systemFields as $field => $details)
                                <option value="{{ $field }}">{{ $field }} ({{ $details['description'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input id="hasTransformation" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" x-model="hasTransformation">
                                <label for="hasTransformation" class="ml-2 block text-sm text-gray-900">Dönüşüm Kullan</label>
                            </div>
                        </div>
                        
                        <div x-show="hasTransformation" class="mb-4 space-y-4 border-t border-gray-200 pt-4">
                            <div>
                                <label for="transformationType" class="block text-sm font-medium text-gray-700">Dönüşüm Tipi</label>
                                <select id="transformationType" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" x-model="transformationType">
                                    <option value="none">-- Dönüşüm tipi seçin --</option>
                                    <option value="date_format">Tarih Formatı</option>
                                    <option value="currency">Para Birimi</option>
                                    <option value="boolean">Boolean (Evet/Hayır)</option>
                                    <option value="fixed">Sabit Değer</option>
                                    <option value="custom">Özel Dönüşüm</option>
                                </select>
                            </div>
                            
                            <!-- Tarih Formatı Dönüşümü -->
                            <div x-show="transformationType === 'date_format'">
                                <label for="dateFormat" class="block text-sm font-medium text-gray-700">Tarih Formatı</label>
                                <select id="dateFormat" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" x-model="dateFormat">
                                    <option value="">-- Format seçin --</option>
                                    @foreach($this->mappingService->getTransformationOptions()['date_format'] as $format)
                                    <option value="{{ $format }}">{{ $format }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Para Birimi Dönüşümü -->
                            <div x-show="transformationType === 'currency'" class="space-y-4">
                                <div>
                                    <label for="currencyFrom" class="block text-sm font-medium text-gray-700">Kaynak Para Birimi</label>
                                    <select id="currencyFrom" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" x-model="currencyFrom">
                                        <option value="">-- Para birimi seçin --</option>
                                        @foreach($this->mappingService->getAvailableCurrencies() as $code => $name)
                                        <option value="{{ $code }}">{{ $code }} - {{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="currencyTo" class="block text-sm font-medium text-gray-700">Hedef Para Birimi</label>
                                    <select id="currencyTo" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" x-model="currencyTo">
                                        @foreach($this->mappingService->getAvailableCurrencies() as $code => $name)
                                        <option value="{{ $code }}">{{ $code }} - {{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Boolean Formatı -->
                            <div x-show="transformationType === 'boolean'">
                                <label for="booleanFormat" class="block text-sm font-medium text-gray-700">Boolean Formatı</label>
                                <select id="booleanFormat" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" x-model="booleanFormat">
                                    <option value="">-- Format seçin --</option>
                                    @foreach($this->mappingService->getTransformationOptions()['boolean'] as $format)
                                    <option value="{{ $format }}">{{ $format }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Sabit Değer -->
                            <div x-show="transformationType === 'fixed'">
                                <label for="fixedValue" class="block text-sm font-medium text-gray-700">Sabit Değer</label>
                                <input type="text" id="fixedValue" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" x-model="fixedValue" placeholder="27">
                            </div>
                            
                            <!-- Özel Dönüşüm -->
                            <div x-show="transformationType === 'custom'">
                                <label for="customCode" class="block text-sm font-medium text-gray-700">Özel Dönüşüm Kodu</label>
                                <textarea id="customCode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" x-model="customCode" rows="3" placeholder="// $value değişkeni üzerinde işlem yapın&#10;return strtoupper($value);"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 flex justify-between">
                    <button type="button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" @click="open = false">
                        İptal
                    </button>
                    <div>
                        <button type="button" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" @click="systemField = ''; save()">
                            Eşleştirmeyi Kaldır
                        </button>
                        <button type="button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" @click="save()">
                            Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>