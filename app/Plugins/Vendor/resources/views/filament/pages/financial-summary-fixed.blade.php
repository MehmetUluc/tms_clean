<x-filament::page>
    <div class="mb-5">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Finansal Özet</h1>
        <p class="text-gray-500 dark:text-gray-400">İşletmenizin genel finansal performansına ilişkin veriler</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6 p-4">
        {{ $this->form }}
    </div>

    @livewire(\App\Plugins\Vendor\Widgets\FinancialStatsWidget::class, ['summary' => $summary])

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-6">
        <div class="col-span-1 md:col-span-3">
            @livewire(\App\Plugins\Vendor\Widgets\RevenueChartWidget::class)
        </div>
        
        <div class="col-span-1">
            @livewire(\App\Plugins\Vendor\Widgets\TransactionTypeWidget::class)
        </div>
        
        <div class="col-span-1 md:col-span-2">
            @livewire(\App\Plugins\Vendor\Widgets\MonthlyBreakdownWidget::class)
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    İşlem Geçmişi
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Seçilen zaman aralığındaki tüm işlemler
                </p>
            </div>
            
            <div class="inline-flex rounded-md shadow-sm">
                <button onclick="$dispatch('changeTab', { tab: 'transactions' })" 
                    class="px-3 py-2 text-sm font-medium {{ $activeTab == 'transactions' ? 'text-white bg-blue-600' : 'text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-200' }} border border-gray-300 rounded-l-md dark:border-gray-600">
                    İşlemler
                </button>
                <button onclick="$dispatch('changeTab', { tab: 'payment_requests' })" 
                    class="px-3 py-2 text-sm font-medium {{ $activeTab == 'payment_requests' ? 'text-white bg-blue-600' : 'text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-200' }} border border-gray-300 dark:border-gray-600">
                    Ödeme Talepleri
                </button>
                <button onclick="$dispatch('changeTab', { tab: 'payments' })" 
                    class="px-3 py-2 text-sm font-medium {{ $activeTab == 'payments' ? 'text-white bg-blue-600' : 'text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-200' }} border border-gray-300 rounded-r-md dark:border-gray-600">
                    Ödemeler
                </button>
            </div>
        </div>
        <div class="p-2">
            {{ $this->table }}
        </div>
    </div>

    <!-- Grafikler için gereken JS bileşenleri -->
    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on('chartDataUpdated', (data) => {
                // Verileri session'a sakla ki widget'lar kullanabilsin
                if (data.chartData) {
                    window.sessionStorage.setItem('vendorChartData', JSON.stringify(data.chartData));
                }
                
                if (data.monthlyBreakdown) {
                    window.sessionStorage.setItem('vendorMonthlyBreakdown', JSON.stringify(data.monthlyBreakdown));
                }
                
                if (data.transactionTypeBreakdown) {
                    window.sessionStorage.setItem('vendorTypeBreakdown', JSON.stringify(data.transactionTypeBreakdown));
                }
                
                // Widget'ları yenile
                Livewire.dispatch('refreshCharts');
            });
        });
    </script>
</x-filament::page>