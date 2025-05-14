<div class="relative">
    <select wire:change="updateSort($event.target.value)" 
            class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500">
        @foreach($sortOptions as $value => $label)
            <option value="{{ $value }}" {{ $sortBy === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>