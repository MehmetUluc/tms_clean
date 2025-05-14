<div class="mt-4">
    <div class="flex items-center gap-4">
        <button type="button" wire:click="searchAvailability" class="filament-button filament-button-size-md inline-flex items-center justify-center py-2 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 -ml-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Check Availability
        </button>
        
        <p class="text-sm text-gray-600">
            Or use the "Next" button to automatically search for rooms.
        </p>
    </div>
    
    @if(!empty($this->availableRooms))
        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Found <span class="font-semibold">{{ count($this->availableRooms) }}</span> available room types. Click "Next" to continue.
            </div>
        </div>
    @endif
</div>