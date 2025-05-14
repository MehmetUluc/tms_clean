<x-filament::page>
    <div class="space-y-6">
        
        <div class="p-4 rounded-xl bg-primary-50 border border-primary-200 dark:bg-primary-950 dark:border-primary-800">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 pt-0.5">
                    <x-filament::icon
                        alias="panels::pages.manage-theme-settings.info-icon"
                        icon="heroicon-o-information-circle"
                        class="w-5 h-5 text-primary-500 dark:text-primary-400"
                    />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-primary-800 dark:text-primary-300">
                        Tema Yöneticisi Hakkında
                    </h3>
                    <div class="mt-1 text-sm text-primary-700 dark:text-primary-400">
                        <p>
                            B2C temanızın görünümünü buradan yönetebilirsiniz. Renkleri, yazı tiplerini, logoları ve diğer görsel unsurları düzenleyebilirsiniz.
                            Yapılan değişiklikler anında uygulanacaktır.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4 xl:gap-8">
            <!-- Filament Formu - Sol Taraf -->
            <div class="col-span-12 xl:col-span-5 space-y-4">
                <div class="p-2 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <form wire:submit="save" class="space-y-6">
                        {{ $this->form }}
                        
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-b-lg border-t border-gray-200 dark:border-gray-700 flex items-center justify-between gap-4">
                            <div>
                                <x-filament::button
                                    type="button"
                                    color="danger"
                                    wire:click="$dispatch('open-modal', { id: 'reset-theme-settings' })"
                                    class="filament-button-danger"
                                >
                                    <span class="flex items-center gap-1">
                                        <x-filament::icon
                                            alias="buttons.reset-settings.icon"
                                            icon="heroicon-m-arrow-path"
                                            class="h-5 w-5"
                                        />
                                        Varsayılanlara Sıfırla
                                    </span>
                                </x-filament::button>
                            </div>
    
                            <x-filament::button type="submit">
                                <span class="flex items-center gap-1">
                                    <x-filament::icon
                                        alias="buttons.save.icon"
                                        icon="heroicon-m-check"
                                        class="h-5 w-5"
                                    />
                                    {{ __('Değişiklikleri Kaydet') }}
                                </span>
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Önizleme - Sağ Taraf -->
            <div class="col-span-12 xl:col-span-7">
                <div class="sticky top-4 space-y-4">
                    <!-- Önizleme Kartı -->
                    <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                        <livewire:color-preview />
                    </div>
                    
                    <!-- Yardım ve İpuçları Kartı -->
                    <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">İpuçları ve Yardım</h3>
                        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-start gap-2">
                                <div class="rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 flex items-center justify-center h-5 w-5 flex-shrink-0 mt-0.5">
                                    <span class="text-xs">1</span>
                                </div>
                                <p><strong>Hızlı Başlangıç:</strong> Hazır bir renk paleti seçerek başlayabilirsiniz. "Renkler" sekmesindeki "Hazır Palet" seçeneğini kullanın.</p>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <div class="rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 flex items-center justify-center h-5 w-5 flex-shrink-0 mt-0.5">
                                    <span class="text-xs">2</span>
                                </div>
                                <p><strong>Canlı Önizleme:</strong> Sağdaki önizleme penceresinde yaptığınız değişiklikleri anında görebilirsiniz.</p>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <div class="rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 flex items-center justify-center h-5 w-5 flex-shrink-0 mt-0.5">
                                    <span class="text-xs">3</span>
                                </div>
                                <p><strong>Logo Yükleme:</strong> Logolar için şeffaf arka planı olan PNG veya SVG dosyaları kullanmanız önerilir.</p>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <div class="rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 flex items-center justify-center h-5 w-5 flex-shrink-0 mt-0.5">
                                    <span class="text-xs">4</span>
                                </div>
                                <p><strong>Yaptığınız değişiklikleri kaydetmek için:</strong> Sayfanın en altındaki "Kaydet" düğmesini kullanmayı unutmayın.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-filament::modal
        id="reset-theme-settings"
        icon="heroicon-o-exclamation-triangle"
        icon-color="danger"
        title="Varsayılan Ayarlara Dön"
        description="Tüm tema ayarlarını sıfırlamak istediğinizden emin misiniz? Bu işlem geri alınamaz."
        width="md"
    >
        <div class="mt-4 flex flex-wrap gap-3 justify-end">
            <x-filament::button
                color="gray"
                x-on:click="$dispatch('close-modal', { id: 'reset-theme-settings' })"
            >
                İptal
            </x-filament::button>

            <x-filament::button
                color="danger"
                wire:click="reset"
                x-on:click="$dispatch('close-modal', { id: 'reset-theme-settings' })"
            >
                Evet, Sıfırla
            </x-filament::button>
        </div>
    </x-filament::modal>
</x-filament::page>