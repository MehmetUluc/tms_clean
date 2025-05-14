<x-filament::page>
    <x-filament::section>
        <div class="mb-6">
            <h2 class="text-xl font-bold">Rate Plan: {{ $ratePlan->name }}</h2>
            <p class="text-gray-500">{{ $ratePlan->description }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <h3 class="font-medium text-lg mb-2">Rate Plan Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p><span class="font-medium">Minimum Stay:</span> {{ $ratePlan->minimum_stay }} night(s)</p>
                    <p><span class="font-medium">Maximum Stay:</span> {{ $ratePlan->maximum_stay ?? 'No limit' }}</p>
                    <p><span class="font-medium">Status:</span> 
                        @if($ratePlan->is_active)
                            <span class="text-green-600">Active</span>
                        @else
                            <span class="text-red-600">Inactive</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p><span class="font-medium">Refundable:</span> 
                        @if($ratePlan->is_refundable)
                            <span class="text-green-600">Yes</span>
                        @else
                            <span class="text-red-600">No</span>
                        @endif
                    </p>
                    <p><span class="font-medium">Applies to All Rooms:</span> 
                        @if($ratePlan->applies_to_all_rooms)
                            <span class="text-green-600">Yes</span>
                        @else
                            <span class="text-blue-600">Specific Rooms Only</span>
                        @endif
                    </p>
                    <p><span class="font-medium">Priority:</span> {{ $ratePlan->priority }}</p>
                </div>
                <div>
                    <p><span class="font-medium">Valid From:</span> {{ $ratePlan->start_date ? $ratePlan->start_date->format('M d, Y') : 'No start date' }}</p>
                    <p><span class="font-medium">Valid To:</span> {{ $ratePlan->end_date ? $ratePlan->end_date->format('M d, Y') : 'No end date' }}</p>
                    <p><span class="font-medium">Hotel:</span> {{ $ratePlan->hotel->name }}</p>
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <h3 class="text-lg font-medium">Rate Periods</h3>
            <p class="text-gray-500 mb-4">Manage date-based pricing periods for this rate plan. Rate periods define pricing for specific date ranges.</p>

            <div class="mb-4 flex justify-end">
                <x-filament::button wire:click="$dispatch('open-modal', { id: 'add-period-modal' })">
                    <span class="mr-1">+</span> Add Period
                </x-filament::button>
            </div>

            @if($ratePlan && count($ratePlan->ratePeriods) > 0)
                <div class="overflow-hidden overflow-x-auto border border-gray-300 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($ratePlan->ratePeriods as $period)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $period->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $period->start_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $period->end_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $period->base_price ? '$' . number_format($period->base_price, 2) : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($period->status)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button wire:click="$dispatch('open-modal', { id: 'edit-period-modal-{{ $period->id }}' })" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                        <button wire:click="deletePeriod({{ $period->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 bg-white border border-gray-300 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No periods found</h3>
                    <p class="text-gray-500 mb-4">Create your first pricing period to get started.</p>
                    <x-filament::button wire:click="$dispatch('open-modal', { id: 'add-period-modal' })">
                        <span class="mr-1">+</span> Add Period
                    </x-filament::button>
                </div>
            @endif

            <x-filament::modal id="add-period-modal" width="xl">
                <x-slot name="heading">Add New Period</x-slot>

                <div>
                    <div class="p-4">
                        <form wire:submit="addPeriod" class="space-y-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                                    <div class="mt-2">
                                        <input type="text" wire:model="name" id="name" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium leading-6 text-gray-900">Start Date</label>
                                        <div class="mt-2">
                                            <input type="date" wire:model="start_date" id="start_date" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="end_date" class="block text-sm font-medium leading-6 text-gray-900">End Date</label>
                                        <div class="mt-2">
                                            <input type="date" wire:model="end_date" id="end_date" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="base_price" class="block text-sm font-medium leading-6 text-gray-900">Base Price</label>
                                        <div class="mt-2">
                                            <input type="number" wire:model="base_price" id="base_price" step="0.01" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="min_stay" class="block text-sm font-medium leading-6 text-gray-900">Minimum Stay</label>
                                        <div class="mt-2">
                                            <input type="number" wire:model="min_stay" id="min_stay" min="1" value="1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center">
                                        <input id="status" wire:model="status" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                        <label for="status" class="ml-2 block text-sm font-medium leading-6 text-gray-900">Active</label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <button type="button" wire:click="$dispatch('close-modal', { id: 'add-period-modal' })" class="px-3 py-2 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit" class="px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Add Period
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-filament::modal>
        </div>
    </x-filament::section>
</x-filament::page>