<x-filament::page>
    <div class="mb-5">
        <h1 class="text-2xl font-bold">Finansal Özet</h1>
        <p class="text-gray-500">İşletmenizin finansal performansına genel bakış</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6 p-4">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Revenue Card -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-medium">Toplam Gelir</h3>
                <div class="bg-white/10 rounded-full w-10 h-10 flex items-center justify-center">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mt-4">₺1,254,890</p>
            <div class="flex items-center mt-2">
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    +12.5%
                </span>
                <span class="ml-2 text-sm opacity-80">Son 30 günde</span>
            </div>
        </div>

        <!-- Occupancy Card -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-medium">Doluluk Oranı</h3>
                <div class="bg-white/10 rounded-full w-10 h-10 flex items-center justify-center">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mt-4">76.4%</p>
            <div class="flex items-center mt-2">
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    +5.2%
                </span>
                <span class="ml-2 text-sm opacity-80">Geçen aya göre</span>
            </div>
        </div>

        <!-- Rating Card -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-medium">Müşteri Puanı</h3>
                <div class="bg-white/10 rounded-full w-10 h-10 flex items-center justify-center">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mt-4">4.7/5</p>
            <div class="flex text-yellow-300 mt-2">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg class="w-5 h-5 opacity-40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
        </div>

        <!-- Reservations Card -->
        <div class="bg-gradient-to-br from-pink-500 to-rose-600 text-white rounded-xl p-4">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-medium">Rezervasyonlar</h3>
                <div class="bg-white/10 rounded-full w-10 h-10 flex items-center justify-center">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mt-4">1,284</p>
            <div class="flex items-center mt-2">
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    +8.1%
                </span>
                <span class="ml-2 text-sm opacity-80">Son 30 günde</span>
            </div>
        </div>
    </div>

    <!-- Revenue Chart Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Gelir Grafiği
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Son 90 günlük gelir analizi
            </p>
        </div>
        <div class="p-6">
            <div id="revenue-chart" class="h-80 w-full"></div>
        </div>
    </div>

    <!-- Hotel Performance Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center flex-wrap">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Otel Bazlı Performans
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Son 30 günlük performans
                    </p>
                </div>
                <div class="inline-flex rounded-md shadow-sm">
                    <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                        Haftalık
                    </button>
                    <button class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-gray-300 dark:border-gray-600">
                        Aylık
                    </button>
                    <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                        Yıllık
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Otel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Gelir
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Doluluk
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Fiyat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Trend
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-md bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400">
                                    <span class="font-medium">GH</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Grand Hotel İstanbul</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">İstanbul / Beyoğlu</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">₺128,450</div>
                            <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400">▲ %12.3</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-white mr-2">91%</div>
                                <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-600">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: 91%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">₺1,250 <span class="text-xs text-gray-500 dark:text-gray-400">/ gece</span></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <svg class="h-6 w-16 text-emerald-500" viewBox="0 0 100 24" preserveAspectRatio="none">
                                <path d="M0,12 Q20,4 40,16 Q60,24 80,12 T100,16" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400">
                                    <span class="font-medium">BR</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Blue Resort & Spa</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Antalya / Kemer</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">₺105,720</div>
                            <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400">▲ %8.5</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-white mr-2">85%</div>
                                <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-600">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">₺980 <span class="text-xs text-gray-500 dark:text-gray-400">/ gece</span></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <svg class="h-6 w-16 text-emerald-500" viewBox="0 0 100 24" preserveAspectRatio="none">
                                <path d="M0,16 Q20,12 40,8 Q60,4 80,8 T100,4" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                İşlem Geçmişi
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Son 30 gün içindeki tüm işlemler
            </p>
        </div>
        <div class="p-2">
            {{ $this->table }}
        </div>
    </div>

    <!-- Hidden Infolist -->
    <div class="hidden">
        {{ $this->summaryInfolist }}
    </div>

    <!-- Load ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const revenueChartOptions = {
                series: [{
                    name: 'Brüt Gelir',
                    type: 'area',
                    data: [26450, 28520, 27950, 29120, 32750, 35250, 38450, 37520, 39450, 43200, 42650, 44250, 45450, 49750, 48250, 47450, 51850, 52520, 54250, 58450, 57250, 59850, 61250, 65450, 66850, 68450, 70850, 72450, 74850, 76250]
                }, {
                    name: 'Net Gelir',
                    type: 'line',
                    data: [18450, 19520, 19950, 21120, 24750, 26250, 28450, 27520, 28450, 31200, 30650, 32250, 33450, 36750, 35250, 34450, 37850, 38520, 39250, 42450, 41250, 42850, 44250, 46450, 47850, 48450, 49850, 50450, 51850, 53250]
                }],
                chart: {
                    height: 320,
                    type: 'line',
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                    },
                    background: 'transparent',
                    dropShadow: {
                        enabled: true,
                        top: 6,
                        left: 0,
                        blur: 6,
                        opacity: 0.1
                    }
                },
                stroke: {
                    width: [0, 3],
                    curve: 'smooth',
                },
                colors: ['#818cf8', '#10b981'],
                fill: {
                    type: ['gradient', 'solid'],
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 100]
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return '₺' + val.toLocaleString('tr-TR');
                        }
                    }
                },
                xaxis: {
                    type: 'datetime',
                    categories: generateDateRange(30),
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                }
            };
            
            const revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), revenueChartOptions);
            revenueChart.render();
            
            // Helper function to generate date range for chart
            function generateDateRange(days) {
                const dates = [];
                const today = new Date();
                
                for (let i = days; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(today.getDate() - i);
                    dates.push(date.getTime());
                }
                
                return dates;
            }
            
            // Setup real data handler when form filter changes
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('chartDataUpdated', (data) => {
                    if (data && data.revenueData) {
                        revenueChart.updateSeries([
                            { 
                                name: 'Brüt Gelir',
                                data: data.revenueData.gross
                            },
                            {
                                name: 'Net Gelir',
                                data: data.revenueData.net
                            }
                        ]);
                    }
                });
            });
        });
    </script>
</x-filament::page>