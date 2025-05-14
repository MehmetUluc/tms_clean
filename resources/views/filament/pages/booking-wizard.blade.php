<x-filament-panels::page>
    <div class="booking-wizard">
        {{ $this->form }}
        
        @if($this->currentStep === 4)
            <div class="mt-6 flex justify-end">
                <button 
                    type="button" 
                    class="filament-button filament-button-size-md inline-flex items-center justify-center py-2 px-3 rounded-lg font-medium text-white bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:bg-primary-600 dark:focus:ring-offset-0"
                    wire:click="create"
                >
                    Confirm Booking
                </button>
            </div>
        @endif
    </div>
    
    <div class="fixed bottom-4 right-4 bg-white p-4 rounded-lg shadow-lg border border-gray-200 z-50">
        <h3 class="text-sm font-medium mb-2">Debug Panel</h3>
        <p class="text-xs mb-2">Current Step: {{ $this->currentStep }}</p>
        
        <div class="flex space-x-2">
            <button 
                type="button" 
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded" 
                wire:click="forceMoveToStep(1)"
            >
                Go to Step 1
            </button>
            
            <button 
                type="button" 
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded" 
                wire:click="forceMoveToStep(2)"
            >
                Go to Step 2
            </button>
            
            <button 
                type="button" 
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded" 
                wire:click="forceMoveToStep(3)"
            >
                Go to Step 3
            </button>
            
            <button 
                type="button" 
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded" 
                wire:click="forceMoveToStep(4)"
            >
                Go to Step 4
            </button>
        </div>

        <div class="mt-3 border-t pt-3">
            <div class="flex space-x-2">
                <button 
                    type="button" 
                    class="px-3 py-1 bg-green-600 text-white text-xs rounded" 
                    wire:click="searchAvailability"
                >
                    Search Availability
                </button>
                
                <button 
                    type="button" 
                    class="px-3 py-1 bg-green-600 text-white text-xs rounded" 
                    wire:click="create"
                >
                    Confirm Booking
                </button>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Update step based on Livewire component state
            @this.on('stepChanged', (step) => {
                console.log('Step changed to:', step);
            });
        });
    </script>
</x-filament-panels::page>