document.addEventListener('DOMContentLoaded', function() {
    try {
        initColorPreview();
        console.log('Theme Manager: Color preview initialized');
    } catch (error) {
        console.error('Theme Manager: Error initializing color preview', error);
    }
    
    // Debug olayını dinle - Livewire 3 için
    document.addEventListener('livewire:init', () => {
        Livewire.on('debug-colors', (event) => {
            console.log('Theme Manager: Current colors', event);
        });
    });
});

/**
 * Filament form renklerini Livewire bileşenine gönderen kod
 */
function initColorPreview() {
    // Renk seçicileri izle
    const colorMapping = {
        'data[color_primary]': 'primaryColor',
        'data[color_secondary]': 'secondaryColor',
        'data[color_accent]': 'accentColor',
        'data[color_success]': 'successColor',
        'data[color_danger]': 'dangerColor',
        'data[color_warning]': 'warningColor',
        'data[color_info]': 'infoColor',
        'data[color_text_primary]': 'textPrimaryColor',
        'data[color_text_secondary]': 'textSecondaryColor',
        'data[color_text_muted]': 'textMutedColor',
        'data[color_background]': 'backgroundColor',
        'data[color_border]': 'borderColor',
        'data[color_header_bg]': 'headerBgColor',
        'data[color_header_text]': 'headerTextColor',
        'data[color_footer_bg]': 'footerBgColor',
        'data[color_footer_text]': 'footerTextColor'
    };
    
    // İlk yükleme için renkleri değişkenlere ata
    function initializeColorValues() {
        Object.keys(colorMapping).forEach(function(formField) {
            const input = document.querySelector(`[name="${formField}"]`);
            if (input && input.value) {
                // Livewire bileşenini bul ve rengi güncelle
                const component = getLivewireComponent();
                if (component) {
                    component.call('updateColor', colorMapping[formField], input.value);
                }
            }
        });
    }
    
    // Livewire bileşeni hazır olduğunda renkleri yükle
    if (window.Livewire) {
        document.addEventListener('livewire:load', initializeColorValues);
    } else {
        // Livewire yüklenmemişse, kısa bir bekleme sonrası dene
        const checkInterval = setInterval(function() {
            if (window.Livewire) {
                clearInterval(checkInterval);
                initializeColorValues();
            }
        }, 100);
        
        // En kötü durumda 2 saniye sonra temizle
        setTimeout(function() { 
            clearInterval(checkInterval);
            initializeColorValues(); // Son bir deneme daha yap
        }, 2000);
    }
    
    // Renk seçicilerini izleme
    document.addEventListener('input', function(e) {
        const inputName = e.target.getAttribute('name');
        
        if (inputName && colorMapping[inputName]) {
            const component = getLivewireComponent();
            if (component) {
                component.call('updateColor', colorMapping[inputName], e.target.value);
            }
        }
    });
    
    // Livewire bileşenini bulma yardımcı fonksiyonu - Livewire 3 için uyarlandı
    function getLivewireComponent() {
        try {
            // Livewire 3'te tüm bileşenlere tek bir API üzerinden erişilebilir
            if (window.Livewire) {
                // İlk olarak, Livewire bileşenlerini içeren elementleri bul
                const colorPreviewElements = document.querySelectorAll('[wire\\:id]');
                
                for (const element of colorPreviewElements) {
                    // Bileşen adını kontrol et
                    if (element.getAttribute('wire:id') && 
                        (element.innerHTML.includes('Canlı Renk Önizleme') || 
                         element.innerHTML.includes('color-preview'))) {
                        
                        // Livewire 3'te bileşenlere erişim
                        const componentId = element.getAttribute('wire:id');
                        // Livewire.find yerine doğrudan Alpine.evaluate kullanabiliriz
                        console.log('Theme Manager: Found Livewire component with ID', componentId);
                        
                        // Doğrudan bileşene dispatch yapabiliriz
                        return {
                            call: function(method, ...params) {
                                // Livewire.dispatch çağrısı
                                console.log(`Theme Manager: Calling ${method} with params`, params);
                                // Livewire 3 dispatchTo kullanımı
                                window.Livewire.dispatch(method, ...params);
                            }
                        };
                    }
                }
            }
            
            console.warn('Theme Manager: No Livewire component found for color preview');
            return null;
        } catch (error) {
            console.error('Theme Manager: Error finding Livewire component', error);
            return null;
        }
    }
}