<x-filament::page>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">{{ $vendor->company_name }} - Financial Summary</h2>
            <p class="text-gray-500 mt-1">Financial overview and transaction history</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="mb-6">
        {{ $this->filterForm }}
    </div>

    <!-- Financial Summary -->
    <div class="mb-6">
        {{ $this->summaryInfolist }}
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }">
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px" role="tablist">
                <li class="mr-2" role="presentation">
                    <button
                        class="inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg"
                        :class="activeTab === 'transactions' ? 'border-b-2 border-primary-500 text-primary-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'"
                        x-on:click="activeTab = 'transactions'"
                        type="button"
                    >
                        Transactions
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button
                        class="inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg"
                        :class="activeTab === 'payment_requests' ? 'border-b-2 border-primary-500 text-primary-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'"
                        x-on:click="activeTab = 'payment_requests'"
                        type="button"
                    >
                        Payment Requests
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button
                        class="inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg"
                        :class="activeTab === 'payments' ? 'border-b-2 border-primary-500 text-primary-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'"
                        x-on:click="activeTab = 'payments'"
                        type="button"
                    >
                        Payments
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="mt-4">
            <div x-show="activeTab === 'transactions'">
                {{ $this->transactionsTable }}
            </div>
            <div x-show="activeTab === 'payment_requests'" x-cloak>
                {{ $this->paymentRequestsTable }}
            </div>
            <div x-show="activeTab === 'payments'" x-cloak>
                {{ $this->paymentsTable }}
            </div>
        </div>
    </div>
</x-filament::page>