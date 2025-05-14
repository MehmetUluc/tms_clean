<div class="flex items-center gap-4 p-4 mb-4 bg-yellow-50 border border-yellow-200 rounded-lg">
    <div>
        <h3 class="text-sm font-medium text-yellow-900">Debug Controls</h3>
        <p class="text-xs text-yellow-700 mb-2">Use these buttons to directly trigger actions for testing</p>
        
        <div class="flex gap-2">
            <button type="button" wire:click="searchAvailability" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs rounded-md">
                Force Search Rooms
            </button>
            
            <button type="button" wire:click="nextStep" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded-md">
                Force Next Step
            </button>
            
            <button type="button" wire:click="previousStep" class="px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded-md">
                Force Previous Step
            </button>
        </div>
    </div>
</div>