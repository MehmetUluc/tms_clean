// FilePond liste görünümünü otomatik olarak düzeltmek için betik
document.addEventListener('DOMContentLoaded', function() {
    // Stil nesnesini oluştur
    const style = document.createElement('style');
    style.id = 'filepond-style-fixer';
    style.innerHTML = `
        /* FilePond Grid Düzeni - JS ile enjekte edilen */
        .filepond--list {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Liste öğelerinin transform stillerini temizleme */
        .filepond--item {
            position: relative !important;
            transform: none !important;
            height: auto !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 0 100% 0 !important; /* Kare yapmak için */
            left: 0 !important;
            top: 0 !important;
        }
        
        /* Tüm içerik öğelerinin mutlak konumlandırılması */
        .filepond--item > fieldset,
        .filepond--item > .filepond--panel {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        /* Panel öğelerinin düzeltilmesi */
        .filepond--panel-top.filepond--item-panel,
        .filepond--panel-center.filepond--item-panel,
        .filepond--panel-bottom.filepond--item-panel {
            transform: none !important;
        }
        
        .filepond--panel-center.filepond--item-panel {
            top: 0 !important;
            height: 100% !important;
        }
        
        .filepond--panel-bottom.filepond--item-panel {
            bottom: 0 !important;
            top: auto !important;
        }
        
        /* Dosya bilgisi ve durum bilgisi gizleme */
        .filepond--file-info,
        .filepond--file-status {
            display: none !important;
        }
        
        /* Düğmelerin konumlandırılması */
        .filepond--file-action-button.filepond--action-remove-item {
            top: 5px !important;
            left: 5px !important;
        }
        
        .filepond--file-action-button.filepond--action-edit-item {
            bottom: 5px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .filepond--list {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        
        @media (max-width: 480px) {
            .filepond--list {
                grid-template-columns: repeat(1, 1fr) !important;
            }
        }
    `;
    
    // Dom'a ekle - head'e eklendiğinde daha yüksek önceliğe sahip olur
    document.head.appendChild(style);
    
    // MutationObserver kullanarak formlara dinamik olarak stiller uygula
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.classList && (node.classList.contains('filepond--list') || node.querySelector('.filepond--list'))) {
                            // Stil uygulandığından emin ol
                            document.head.appendChild(style);
                        }
                    }
                });
            }
        });
    });
    
    // Tüm DOM değişikliklerini izle
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});