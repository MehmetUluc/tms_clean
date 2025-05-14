<x-filament::section class="bg-gradient-to-br from-white/50 to-white/30 dark:from-gray-900/50 dark:to-gray-800/30 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Sistem Özeti
            </h2>
            
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                TMS Otel ve Seyahat Yönetim Sistemi
            </p>
        </div>
        
        <div class="flex items-center">
            <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-success-500"></span>
            </span>
            <span class="ml-2 text-xs font-medium text-gray-500 dark:text-gray-400">Çevrimiçi</span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 mt-6">
        <!-- Sistem Card -->
        <div class="group flex flex-col gap-y-3 rounded-xl bg-white/80 p-6 dark:bg-gray-800/80 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-x-3">
                <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-sm group-hover:shadow-primary-500/20 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Sistem
                </h3>
            </div>
            
            <div class="mt-2 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">PHP</span>
                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ PHP_VERSION }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Laravel</span>
                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ Illuminate\Foundation\Application::VERSION }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Filament</span>
                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ \Composer\InstalledVersions::getVersion('filament/filament') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Kullanıcılar Card -->
        <div class="group flex flex-col gap-y-3 rounded-xl bg-white/80 p-6 dark:bg-gray-800/80 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-x-3">
                <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-gradient-to-br from-success-500 to-success-600 text-white shadow-sm group-hover:shadow-success-500/20 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Kullanıcılar
                </h3>
            </div>
            
            <div class="mt-2">
                <div class="flex items-center">
                    <div class="text-3xl font-bold text-success-600 dark:text-success-400">
                        {{ \App\Models\User::count() }}
                    </div>
                    <div class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                        Toplam Kullanıcı
                    </div>
                </div>
                
                <div class="mt-4 h-2 w-full bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="h-2 bg-gradient-to-r from-success-400 to-success-500 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>
        
        <!-- Yardım Card -->
        <div class="group flex flex-col gap-y-3 rounded-xl bg-white/80 p-6 dark:bg-gray-800/80 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-x-3">
                <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-gradient-to-br from-warning-500 to-warning-600 text-white shadow-sm group-hover:shadow-warning-500/20 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Yardım
                </h3>
            </div>
            
            <div class="mt-2 space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Teknik destek ve yardım için bizimle iletişime geçebilirsiniz.
                </p>
                
                <a href="mailto:support@example.com" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-warning-500 to-warning-600 rounded-lg hover:from-warning-600 hover:to-warning-700 focus:ring-4 focus:ring-warning-300 dark:focus:ring-warning-800 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    E-posta Gönder
                </a>
            </div>
        </div>
    </div>
</x-filament::section>