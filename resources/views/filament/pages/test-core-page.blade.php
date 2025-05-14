<x-filament-panels::page>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow">
        <h2 class="text-2xl font-bold mb-4">Core Plugin Test</h2>
        
        <div class="space-y-4">
            <div class="p-4 bg-green-100 dark:bg-green-900 rounded-lg">
                <h3 class="text-lg font-semibold">Plugin Bilgileri</h3>
                <div class="mt-2">
                    @php
                        $corePlugin = app()->make(\App\Plugins\Core\CorePlugin::class);
                        $serviceProvider = app()->getProvider(\App\Plugins\Core\CoreServiceProvider::class);
                    @endphp
                    
                    @if($corePlugin)
                        <div class="text-green-700 dark:text-green-300">✅ Core Plugin başarıyla yüklendi!</div>
                        <div class="mt-2">Plugin ID: {{ $corePlugin->getId() }}</div>
                    @else
                        <div class="text-red-700 dark:text-red-300">❌ Core Plugin yüklenemedi!</div>
                    @endif
                    
                    @if($serviceProvider)
                        <div class="text-green-700 dark:text-green-300">✅ Core ServiceProvider başarıyla kaydedildi!</div>
                    @else
                        <div class="text-red-700 dark:text-red-300">❌ Core ServiceProvider kaydedilemedi!</div>
                    @endif
                </div>
            </div>
            
            <div class="p-4 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <h3 class="text-lg font-semibold">Plugin Bileşenleri</h3>
                <div class="mt-2 space-y-2">
                    @if(class_exists(\App\Plugins\Core\src\Models\BaseModel::class))
                        <div class="text-green-700 dark:text-green-300">✅ BaseModel sınıfı mevcut</div>
                    @else
                        <div class="text-red-700 dark:text-red-300">❌ BaseModel sınıfı bulunamadı</div>
                    @endif
                    
                    @if(class_exists(\App\Plugins\Core\src\Filament\Components\ImageGalleryUpload::class))
                        <div class="text-green-700 dark:text-green-300">✅ ImageGalleryUpload bileşeni mevcut</div>
                    @else
                        <div class="text-red-700 dark:text-red-300">❌ ImageGalleryUpload bileşeni bulunamadı</div>
                    @endif
                    
                    @if(class_exists(\App\Plugins\Core\Services\PluginLoader::class))
                        <div class="text-green-700 dark:text-green-300">✅ PluginLoader servisi mevcut</div>
                        @php
                            $pluginLoader = app()->make(\App\Plugins\Core\Services\PluginLoader::class);
                            $plugins = $pluginLoader->loadAllPlugins();
                        @endphp
                        <div class="mt-2">
                            <div class="font-medium">Yüklenen Plugins:</div>
                            <ul class="list-disc list-inside ml-2">
                                @forelse($plugins as $id => $plugin)
                                    <li>{{ $id }}</li>
                                @empty
                                    <li class="text-gray-500">Henüz plugin yüklenmedi</li>
                                @endforelse
                            </ul>
                        </div>
                    @else
                        <div class="text-red-700 dark:text-red-300">❌ PluginLoader servisi bulunamadı</div>
                    @endif
                </div>
            </div>
            
            <div class="p-4 bg-purple-100 dark:bg-purple-900 rounded-lg">
                <h3 class="text-lg font-semibold">Yapılandırma</h3>
                <div class="mt-2">
                    @if(config()->has('core'))
                        <div class="text-green-700 dark:text-green-300">✅ Core yapılandırması yüklendi</div>
                        <div class="mt-2">
                            <div class="font-medium">Örnek Ayarlar:</div>
                            <ul class="list-disc list-inside ml-2">
                                <li>Plugin Dizini: {{ config('core.plugins_directory') }}</li>
                                <li>Tenant İzolasyonu: {{ config('core.tenant_isolation') ? 'Aktif' : 'Pasif' }}</li>
                                <li>Form Bileşenleri: {{ config('core.form_components.file_upload.show_as_grid') ? 'Grid Görünümü Aktif' : 'Grid Görünümü Pasif' }}</li>
                            </ul>
                        </div>
                    @else
                        <div class="text-red-700 dark:text-red-300">❌ Core yapılandırması yüklenemedi</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>