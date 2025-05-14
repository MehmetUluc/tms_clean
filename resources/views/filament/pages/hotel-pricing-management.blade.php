<x-filament::page>
    <x-filament::section>
        <form wire:submit="submit" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    Load Hotel
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    @if($hotel)
        <x-filament::section>
            <div class="mb-6">
                <h2 class="text-xl font-bold">Hotel: {{ $hotel->name }}</h2>
                <p class="text-gray-500">Manage pricing configurations for this hotel</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="font-medium text-lg mb-2">Hotel Information</h3>
                    <div class="space-y-1">
                        <p><span class="font-medium">Name:</span> {{ $hotel->name }}</p>
                        <p><span class="font-medium">Type:</span> {{ optional($hotel->type)->name ?? 'N/A' }}</p>
                        <p><span class="font-medium">Region:</span> {{ optional($hotel->region)->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="font-medium text-lg mb-2">Pricing Summary</h3>
                    <div class="space-y-1">
                        <p><span class="font-medium">Active Rate Plans:</span> {{ $hotel->ratePlans()->where('is_active', true)->count() }}</p>
                        <p><span class="font-medium">Board Types:</span> {{ $hotel->hotelBoardTypes()->count() }}</p>
                        <p><span class="font-medium">Refundable Policy:</span> {{ ucfirst($hotel->refund_policy ?? 'Not set') }}</p>
                        <p><span class="font-medium">Selected Date Range:</span> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Board Types Section has been removed as requested -->

                <!-- Rate Plans Section -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Rate Plans</h3>
                        <div class="text-sm text-gray-500">Manage pricing rate plans for this hotel</div>
                    </div>

                    @if(count($ratePlans) > 0)
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Name</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Board Type</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($ratePlans as $ratePlan)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                {{ $ratePlan->name }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ optional(optional($ratePlan->hotelBoardType)->boardType)->name ?? 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                @if($ratePlan->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <a href="{{ url('/admin/resources/pricing-v2-rate-plans/' . $ratePlan->id . '/edit') }}" class="text-primary-600 hover:text-primary-900">
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 text-right">
                            <a href="{{ url('/admin/resources/pricing-v2-rate-plans/create?hotel_id=' . $hotel->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create New Rate Plan
                            </a>
                        </div>
                    @else
                        <div class="text-center p-6 text-gray-500 bg-gray-50 rounded-lg">
                            <p class="mb-4">No rate plans found for this hotel.</p>
                            <a href="{{ url('/admin/resources/pricing-v2-rate-plans/create?hotel_id=' . $hotel->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create First Rate Plan
                            </a>
                        </div>
                    @endif

                    <div class="mt-3 text-sm text-gray-500">
                        <p>Create rate plans to define different pricing structures for your hotel.</p>
                        <p>Each rate plan is associated with a specific board type and can apply to specific rooms.</p>
                    </div>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament::page>