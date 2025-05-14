<x-filament-panels::page>
    <div class="booking-wizard-v2">
        @csrf
        <form method="POST" wire:submit.prevent>
            {{ $this->form }}
        </form>
    </div>
    
    <div class="fixed bottom-4 right-4 bg-white p-4 rounded-lg shadow-lg border border-gray-200 z-50">
        <h3 class="text-sm font-medium mb-2">Debug Panel</h3>
        <p class="text-xs mb-2">Current Step: {{ $this->currentStep }}</p>
        
        <div class="flex space-x-2">
            <button 
                type="button" 
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded" 
                wire:click="$set('currentStep', 1)"
            >
                Go to Step 1
            </button>
            
            <button 
                type="button" 
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded" 
                wire:click="$set('currentStep', 2)"
            >
                Go to Step 2
            </button>
            
            <button 
                type="button" 
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded" 
                wire:click="$set('currentStep', 3)"
            >
                Go to Step 3
            </button>
            
            <button 
                type="button" 
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded" 
                wire:click="$set('currentStep', 4)"
            >
                Go to Step 4
            </button>
        </div>
        
        <div class="mt-3 border-t pt-3">
            <div class="flex flex-col space-y-2">
                <p class="text-xs font-medium">Debug Info:</p>
                <p class="text-xs">Selected Region: {{ $this->selectedRegion ?? 'none' }}</p>
                <p class="text-xs">Selected Hotel: {{ $this->selectedHotel ?? 'none' }}</p>
                <p class="text-xs">Hotels Count: {{ count($this->availableHotels) }}</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>