<div class="color-preview-container">
    <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
        <h4 class="font-medium text-lg">Canlı Renk Önizleme</h4>
        <div class="flex space-x-2">
            <button type="button" wire:click="toggleDarkMode" class="text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-700 transition-all transform hover:scale-105">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    {{ $darkMode ? 'Açık Mod' : 'Koyu Mod' }}
                </span>
            </button>
            <button type="button" wire:click="$dispatch('applyColorsToForm')" class="text-xs bg-primary-50 hover:bg-primary-100 dark:bg-primary-900 dark:hover:bg-primary-800 text-primary-600 dark:text-primary-400 px-2 py-1 rounded-md border border-primary-200 dark:border-primary-700 transition-all transform hover:scale-105">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Forma Uygula
                </span>
            </button>
        </div>
    </div>

    <!-- Renk Tema Seçici -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <h5 class="font-medium mb-3 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400">Hazır Renk Temaları</h5>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach ($colorThemes as $themeKey => $theme)
                <button 
                    wire:click="applyColorTheme('{{ $themeKey }}')" 
                    class="p-1 rounded-lg border {{ $activeColorTheme === $themeKey ? 'border-primary-500 ring-2 ring-primary-200 dark:ring-primary-800' : 'border-gray-200 dark:border-gray-700' }} hover:shadow-md transition-all transform hover:scale-105 group"
                >
                    <div class="rounded-md h-14 w-full overflow-hidden">
                        <div class="h-3 w-full" style="background-color: {{ $theme['primaryColor'] }}"></div>
                        <div class="h-3 w-full" style="background-color: {{ $theme['secondaryColor'] }}"></div>
                        <div class="h-3 w-full" style="background-color: {{ $theme['accentColor'] }}"></div>
                        <div class="h-5 w-full flex items-center justify-center text-white text-xs" style="background-color: {{ $theme['headerBgColor'] }}">
                            {{ $theme['name'] }}
                        </div>
                    </div>
                </button>
            @endforeach
            <!-- Rastgele renk teması -->
            <button 
                wire:click="generateRandomColors" 
                class="p-1 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all transform hover:scale-105 group"
            >
                <div class="rounded-md h-14 w-full overflow-hidden bg-gradient-to-br from-pink-500 via-purple-500 to-blue-500">
                    <div class="h-14 w-full flex flex-col items-center justify-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <span class="text-xs mt-1">Rastgele</span>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="space-y-6">
            <!-- Ana Renkler Gösterimi -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <h5 class="font-medium mb-3 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400 flex justify-between items-center">
                    <span>Ana Renkler</span>
                    <button wire:click="debugColors" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </h5>
                <div class="grid grid-cols-3 gap-3">
                    @foreach ([
                        'primary' => ['Ana Renk', $primaryColor],
                        'secondary' => ['İkincil Renk', $secondaryColor],
                        'accent' => ['Vurgu Rengi', $accentColor]
                    ] as $colorKey => $colorData)
                        <div class="flex flex-col items-center transform transition-all hover:scale-105">
                            <div class="w-16 h-16 rounded-full mb-2 shadow-lg border-4 border-white dark:border-gray-700 cursor-pointer" style="background-color: {{ $colorData[1] }}">
                                <div class="opacity-0 hover:opacity-100 bg-black bg-opacity-40 w-full h-full rounded-full flex items-center justify-center transition-opacity">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                    </svg>
                                </div>
                            </div>
                            <span class="text-xs font-medium">{{ $colorData[0] }}</span>
                            <span class="text-xs text-gray-500 mt-1 font-mono">{{ $colorData[1] }}</span>
                            <div class="mt-2 flex flex-wrap justify-center gap-1">
                                @foreach(['100', '300', '500', '700', '900'] as $shade)
                                    <div class="w-5 h-5 rounded-full border border-gray-200 dark:border-gray-600 cursor-pointer hover:scale-110 transition-transform" 
                                         style="background-color: {{ str_replace(['rgb', 'rgba'], ['hsl', 'hsla'], $colorData[1]) }}; 
                                                filter: brightness({{ 2 - (intval($shade) / 500) }});"
                                         title="{{ $colorKey }}-{{ $shade }}"></div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Durum Renkleri -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <h5 class="font-medium mb-3 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400">Durum Renkleri</h5>
                <div class="grid grid-cols-4 gap-3">
                    @foreach ([
                        'success' => ['Başarı', $successColor],
                        'warning' => ['Uyarı', $warningColor],
                        'danger' => ['Tehlike', $dangerColor],
                        'info' => ['Bilgi', $infoColor]
                    ] as $colorKey => $colorData)
                        <div class="flex flex-col items-center">
                            <div class="group relative">
                                <div class="w-14 h-14 rounded-lg mb-2 transform transition-all group-hover:scale-110 shadow-md" style="background-color: {{ $colorData[1] }}"></div>
                                <div class="opacity-0 group-hover:opacity-100 absolute -top-2 -right-2 w-5 h-5 flex items-center justify-center text-xs text-white rounded-full transition-opacity" style="background-color: {{ $colorData[1] }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <span class="text-xs font-medium">{{ $colorData[0] }}</span>
                            <span class="text-xs text-gray-500 mt-1 font-mono">{{ substr($colorData[1], 0, 7) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Metin Renkleri -->
            <div class="p-5 rounded-xl shadow-sm" style="background-color: {{ $backgroundColor }}; border: 1px solid {{ $borderColor }}">
                <h5 class="font-medium mb-4 text-sm uppercase tracking-wider" style="color: {{ $textPrimaryColor }}">Metin Renkleri</h5>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $textPrimaryColor }}"></div>
                        <div class="flex-1">
                            <div class="text-lg font-medium" style="color: {{ $textPrimaryColor }}">Ana Metin</div>
                            <p style="color: {{ $textPrimaryColor }}">Bu bir ana metin örneğidir - başlıklar ve önemli içerikler için kullanılır.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $textSecondaryColor }}"></div>
                        <div class="flex-1">
                            <div class="text-lg font-medium" style="color: {{ $textSecondaryColor }}">İkincil Metin</div>
                            <p style="color: {{ $textSecondaryColor }}">Bu bir ikincil metin örneğidir - açıklamalar ve ara başlıklar için kullanılır.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $textMutedColor }}"></div>
                        <div class="flex-1">
                            <div class="text-lg font-medium" style="color: {{ $textMutedColor }}">Soluk Metin</div>
                            <p style="color: {{ $textMutedColor }}">Bu bir soluk metin örneğidir - ek bilgiler ve notlar için kullanılır.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- UI Önizleme -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <h5 class="font-medium mb-3 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400">Butonlar ve Kullanıcı Arayüzü</h5>
                
                <!-- Butonlar -->
                <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-4 bg-gray-50 dark:bg-gray-900">
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <button class="px-4 py-2 rounded-md text-white font-medium text-sm shadow-sm transform transition-all hover:scale-105" style="background-color: {{ $primaryColor }}">Ana Buton</button>
                        <button class="px-4 py-2 rounded-md text-white font-medium text-sm shadow-sm transform transition-all hover:scale-105" style="background-color: {{ $secondaryColor }}">İkincil Buton</button>
                        <button class="px-4 py-2 rounded-md text-white font-medium text-sm shadow-sm transform transition-all hover:scale-105" style="background-color: {{ $accentColor }}">Vurgu Buton</button>
                        <button class="px-4 py-2 rounded-md font-medium text-sm border shadow-sm transform transition-all hover:scale-105" style="color: {{ $primaryColor }}; border-color: {{ $primaryColor }}; background-color: transparent;">Dış Çizgili</button>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ adjustBrightness($primaryColor, 0.9) }}; color: {{ adjustBrightness($primaryColor, 0.3) }}">
                            Ana Etiket
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ adjustBrightness($secondaryColor, 0.9) }}; color: {{ adjustBrightness($secondaryColor, 0.3) }}">
                            İkincil Etiket
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ adjustBrightness($accentColor, 0.9) }}; color: {{ adjustBrightness($accentColor, 0.3) }}">
                            Vurgu Etiket
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ adjustBrightness($successColor, 0.9) }}; color: {{ adjustBrightness($successColor, 0.3) }}">
                            Başarı
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ adjustBrightness($dangerColor, 0.9) }}; color: {{ adjustBrightness($dangerColor, 0.3) }}">
                            Tehlike
                        </span>
                    </div>
                </div>
                
                <!-- Bildirimler -->
                <div class="space-y-3">
                    <div class="flex items-center p-3 rounded-lg text-white shadow-sm transform transition-all hover:scale-102" style="background-color: {{ $successColor }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>İşlem başarıyla tamamlandı</span>
                    </div>
                    
                    <div class="flex items-center p-3 rounded-lg text-white shadow-sm transform transition-all hover:scale-102" style="background-color: {{ $warningColor }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Dikkat! Bu önemli bir bildirimdir</span>
                    </div>
                    
                    <div class="flex items-center p-3 rounded-lg text-white shadow-sm transform transition-all hover:scale-102" style="background-color: {{ $dangerColor }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>Hata! İşlem tamamlanamadı</span>
                    </div>
                </div>
            </div>

            <!-- Header/Footer -->
            <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md">
                <h5 class="font-medium p-3 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <span>Canlı Site Önizleme</span>
                    <span class="text-xs text-gray-400 font-normal">{{ $darkMode ? 'Koyu Mod' : 'Açık Mod' }}</span>
                </h5>
                
                <div class="overflow-hidden rounded-b-lg">
                    <!-- Header -->
                    <div class="p-4 flex justify-between items-center shadow-sm" style="background-color: {{ $headerBgColor }}; color: {{ $headerTextColor }}">
                        <div class="flex items-center">
                            <span class="font-bold mr-4">SİTE LOGO</span>
                            <div class="hidden sm:flex space-x-4">
                                <span class="font-medium border-b-2" style="border-color: {{ $accentColor }}">Ana Sayfa</span>
                                <span>Oteller</span>
                                <span>Bölgeler</span>
                                <span>İletişim</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-1 rounded text-sm shadow-sm" style="background-color: {{ $accentColor }}; color: white;">Giriş Yap</button>
                        </div>
                    </div>
                    
                    <!-- Hero Section -->
                    <div class="h-36 flex items-center justify-center bg-cover bg-center relative" style="background-image: linear-gradient(to right, {{ adjustOpacity($primaryColor, 0.7) }}, {{ adjustOpacity($secondaryColor, 0.7) }})">
                        <div class="text-center text-white z-10 p-4">
                            <h2 class="text-xl font-bold mb-2">Hoş Geldiniz</h2>
                            <p class="text-sm">En iyi tatil deneyimi için doğru yerdesiniz.</p>
                            <button class="mt-3 px-4 py-2 bg-white rounded-md text-sm font-medium shadow-sm hover:shadow-md transition-shadow" style="color: {{ $primaryColor }}">Hemen Rezervasyon Yap</button>
                        </div>
                    </div>
                    
                    <!-- Sayfa İçeriği Simülasyonu -->
                    <div class="p-4" style="background-color: {{ $backgroundColor }}; min-height: 120px;">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <div class="h-16 rounded-lg flex items-center justify-center border border-gray-200 shadow-sm" style="border-color: {{ $borderColor }}; background-color: white;">
                                    <span style="color: {{ $textSecondaryColor }}">Ana İçerik</span>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <div class="h-16 rounded-lg flex items-center justify-center border border-gray-200 shadow-sm" style="border-color: {{ $borderColor }}; background-color: {{ adjustBrightness($backgroundColor, 0.95) }};">
                                    <span style="color: {{ $textSecondaryColor }}">Yan Menü</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="p-4" style="background-color: {{ $footerBgColor }}; color: {{ $footerTextColor }}">
                        <div class="flex flex-col sm:flex-row justify-between items-center">
                            <div class="mb-2 sm:mb-0">
                                <span class="text-sm">&copy; 2025 Site Adı - Tüm Hakları Saklıdır</span>
                            </div>
                            <div class="flex space-x-4">
                                <span class="text-sm">Hakkımızda</span>
                                <span class="text-sm">Gizlilik</span>
                                <span class="text-sm">İletişim</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Çıkartılabilir Bilgi Kartı -->
            <div class="bg-indigo-50 dark:bg-indigo-900 rounded-xl p-4 shadow-sm border border-indigo-100 dark:border-indigo-800">
                <div class="flex items-start">
                    <div class="flex-shrink-0 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-indigo-800 dark:text-indigo-300">Renk Paletleri Hakkında</h3>
                        <div class="mt-2 text-sm text-indigo-700 dark:text-indigo-400">
                            <p>Yukarıdan hazır renk paletleri seçebilir, karanlık modu açıp kapatabilir ve rastgele renkler oluşturabilirsiniz. Beğendiğiniz renkleri forma uygulamak için "Forma Uygula" butonunu kullanın.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Trigger for Animation on Load -->
    <script>
        document.addEventListener('livewire:load', function() {
            // Trigger a subtle animation effect when the page loads
            document.querySelectorAll('.color-preview-container .rounded-xl').forEach(function(el, index) {
                setTimeout(function() {
                    el.classList.add('animate-pulse');
                    setTimeout(function() {
                        el.classList.remove('animate-pulse');
                    }, 500);
                }, index * 100);
            });
        });
    </script>
</div>

@php
// Helper functions for color manipulation
function adjustBrightness($color, $factor) {
    if (strpos($color, 'rgb') === 0) {
        preg_match('/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*[\d.]+)?\)/', $color, $matches);
        if (count($matches) >= 4) {
            $r = min(255, max(0, intval($matches[1] * $factor)));
            $g = min(255, max(0, intval($matches[2] * $factor)));
            $b = min(255, max(0, intval($matches[3] * $factor)));
            return "rgb($r, $g, $b)";
        }
    } elseif (strpos($color, '#') === 0) {
        $hex = ltrim($color, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2)) * $factor;
        $g = hexdec(substr($hex, 2, 2)) * $factor;
        $b = hexdec(substr($hex, 4, 2)) * $factor;
        $r = min(255, max(0, intval($r)));
        $g = min(255, max(0, intval($g)));
        $b = min(255, max(0, intval($b)));
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
    return $color;
}

function adjustOpacity($color, $opacity) {
    if (strpos($color, 'rgb') === 0) {
        if (strpos($color, 'rgba') === 0) {
            return preg_replace('/rgba?\([^)]+\)/', "rgba($1, $2, $3, $opacity)", $color);
        } else {
            preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $color, $matches);
            if (count($matches) >= 4) {
                return "rgba({$matches[1]}, {$matches[2]}, {$matches[3]}, $opacity)";
            }
        }
    } elseif (strpos($color, '#') === 0) {
        $hex = ltrim($color, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $opacity)";
    }
    return $color;
}
@endphp