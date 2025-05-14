<div class="p-2 mb-2 rounded bg-gray-50 text-sm">
    <p><span class="font-medium">Selected Hotel ID:</span> 
        <span class="{{ $this->selectedHotel ? 'text-green-600' : 'text-red-600' }}">
            {{ $this->selectedHotel ?? 'Not selected' }}
        </span>
    </p>
    
    <div class="mt-2">
        <button 
            type="button" 
            class="px-3 py-1 bg-blue-500 text-white text-xs rounded" 
            wire:click="$set('selectedHotel', '1')"
        >
            Force Hotel ID to 1
        </button>
        
        <button 
            type="button" 
            class="px-3 py-1 bg-blue-500 text-white text-xs rounded ml-2" 
            wire:click="$set('data.hotel_id', '1')"
        >
            Force data.hotel_id to 1
        </button>
    </div>
</div>