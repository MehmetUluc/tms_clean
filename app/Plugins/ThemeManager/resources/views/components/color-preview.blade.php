<div class="p-4 bg-white rounded-lg shadow">
    <h3 class="text-lg font-medium mb-4">Renk Önizleme</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Primary Colors Section -->
        <div class="space-y-4">
            <h4 class="font-medium">Ana Renkler</h4>
            
            <!-- Primary Color -->
            <div class="flex flex-col space-y-2">
                <div class="flex items-center">
                    <div class="w-6 h-6 rounded mr-2" style="background-color: {{ $primaryColor ?? '#3b82f6' }}"></div>
                    <span class="text-sm">Ana Renk</span>
                </div>
                
                <!-- Primary Buttons -->
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-white text-sm rounded" 
                            style="background-color: {{ $primaryColor ?? '#3b82f6' }}">
                        Ana Buton
                    </button>
                    <button class="px-3 py-1 text-sm rounded border" 
                            style="color: {{ $primaryColor ?? '#3b82f6' }}; border-color: {{ $primaryColor ?? '#3b82f6' }}">
                        Ana Buton (Kenarlar)
                    </button>
                </div>
            </div>
            
            <!-- Secondary Color -->
            <div class="flex flex-col space-y-2">
                <div class="flex items-center">
                    <div class="w-6 h-6 rounded mr-2" style="background-color: {{ $secondaryColor ?? '#10b981' }}"></div>
                    <span class="text-sm">İkincil Renk</span>
                </div>
                
                <!-- Secondary Buttons -->
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-white text-sm rounded" 
                            style="background-color: {{ $secondaryColor ?? '#10b981' }}">
                        İkincil Buton
                    </button>
                    <button class="px-3 py-1 text-sm rounded border" 
                            style="color: {{ $secondaryColor ?? '#10b981' }}; border-color: {{ $secondaryColor ?? '#10b981' }}">
                        İkincil Buton (Kenarlar)
                    </button>
                </div>
            </div>
            
            <!-- Accent Color -->
            <div class="flex flex-col space-y-2">
                <div class="flex items-center">
                    <div class="w-6 h-6 rounded mr-2" style="background-color: {{ $accentColor ?? '#f59e0b' }}"></div>
                    <span class="text-sm">Vurgu Rengi</span>
                </div>
                
                <!-- Accent Buttons -->
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-white text-sm rounded" 
                            style="background-color: {{ $accentColor ?? '#f59e0b' }}">
                        Vurgu Buton
                    </button>
                    <button class="px-3 py-1 text-sm rounded border" 
                            style="color: {{ $accentColor ?? '#f59e0b' }}; border-color: {{ $accentColor ?? '#f59e0b' }}">
                        Vurgu Buton (Kenarlar)
                    </button>
                </div>
            </div>
        </div>
        
        <!-- UI Elements Section -->
        <div class="space-y-4">
            <h4 class="font-medium">UI Bileşenleri</h4>
            
            <!-- Text Colors -->
            <div class="space-y-1">
                <p style="color: {{ $textPrimaryColor ?? '#1f2937' }}">Ana Metin Rengi - Lorem ipsum dolor sit amet</p>
                <p style="color: {{ $textSecondaryColor ?? '#4b5563' }}">İkincil Metin Rengi - Lorem ipsum dolor sit amet</p>
                <p style="color: {{ $textMutedColor ?? '#9ca3af' }}">Soluk Metin Rengi - Lorem ipsum dolor sit amet</p>
            </div>
            
            <!-- Card Example -->
            <div class="rounded-lg p-3 border" style="background-color: {{ $backgroundColor ?? '#ffffff' }}; border-color: {{ $borderColor ?? '#e5e7eb' }}">
                <h5 class="font-medium mb-2" style="color: {{ $textPrimaryColor ?? '#1f2937' }}">Kart Örneği</h5>
                <p class="text-sm mb-2" style="color: {{ $textSecondaryColor ?? '#4b5563' }}">Bu bir kart içeriği örneğidir.</p>
                <div class="text-xs" style="color: {{ $textMutedColor ?? '#9ca3af' }}">Son güncelleme: 4 May, 2025</div>
            </div>
            
            <!-- Alerts -->
            <div class="space-y-2">
                <div class="p-2 rounded-md" style="background-color: {{ $successLightColor ?? '#d1fae5' }}; color: {{ $successColor ?? '#059669' }}">
                    <span class="text-sm">Başarı bildirimi örneği</span>
                </div>
                <div class="p-2 rounded-md" style="background-color: {{ $errorLightColor ?? '#fee2e2' }}; color: {{ $errorColor ?? '#dc2626' }}">
                    <span class="text-sm">Hata bildirimi örneği</span>
                </div>
                <div class="p-2 rounded-md" style="background-color: {{ $warningLightColor ?? '#fef3c7' }}; color: {{ $warningColor ?? '#d97706' }}">
                    <span class="text-sm">Uyarı bildirimi örneği</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Header/Footer Preview -->
    <div class="mt-6 space-y-4">
        <h4 class="font-medium">Sayfa Öğeleri</h4>
        
        <!-- Header Preview -->
        <div class="rounded-t-lg p-3" style="background-color: {{ $headerBgColor ?? '#1e40af' }}; color: {{ $headerTextColor ?? '#ffffff' }}">
            <div class="flex justify-between items-center">
                <div class="font-medium">Site Başlığı</div>
                <div class="flex space-x-4">
                    <span>Menü 1</span>
                    <span>Menü 2</span>
                    <span>Menü 3</span>
                </div>
            </div>
        </div>
        
        <!-- Footer Preview -->
        <div class="rounded-b-lg p-3" style="background-color: {{ $footerBgColor ?? '#1e293b' }}; color: {{ $footerTextColor ?? '#f3f4f6' }}">
            <div class="text-center text-sm">
                <div>Telif Hakkı © 2025 - Site Adı</div>
                <div class="flex justify-center space-x-4 mt-1">
                    <span>Hakkımızda</span>
                    <span>İletişim</span>
                    <span>Gizlilik Politikası</span>
                </div>
            </div>
        </div>
    </div>
    
    <p class="mt-4 text-xs text-gray-500">Not: Bu önizleme, seçtiğiniz renk şemasının sayfanızda nasıl görüneceğine dair genel bir fikir verir.</p>
</div>