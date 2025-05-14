<x-filament::page>
    <div>
        {{ $this->form }}
    </div>
    
    <script>
        document.addEventListener('livewire:load', function() {
            // JavaScript event handlers
            Livewire.on('xmlParsingCompleted', function() {
                // XML analizi tamamlandığında bir sonraki adıma geç
                const nextButton = document.querySelector('button[dusk="next-wizard-step-button"]');
                if (nextButton) {
                    nextButton.click();
                }
            });
            
            Livewire.on('mappingGenerated', function() {
                // Eşleştirme oluşturulduğunda bir sonraki adıma geç
                const nextButton = document.querySelector('button[dusk="next-wizard-step-button"]');
                if (nextButton) {
                    nextButton.click();
                }
            });
        });
    </script>
</x-filament::page>