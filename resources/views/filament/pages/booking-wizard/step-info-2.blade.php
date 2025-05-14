<div class="p-4 mb-4 bg-blue-50 border border-blue-200 rounded-lg">
    <h3 class="text-sm font-medium text-blue-900">Room Selection - Step {{ $this->currentStep }}</h3>
    <p class="text-xs text-blue-700">
        Available rooms ({{ count($this->availableRooms) }}): 
        @foreach(array_column($this->availableRooms, 'name') as $name)
            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-1 py-0.5 rounded mr-1">{{ $name }}</span>
        @endforeach
    </p>
</div>