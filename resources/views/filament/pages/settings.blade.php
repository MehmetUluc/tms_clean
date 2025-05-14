<x-filament::page>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow">
        <h2 class="text-2xl font-bold tracking-tight mb-4">Sistem Ayarları</h2>
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium mb-3">Genel Ayarlar</h3>
                    <div class="space-y-3">
                        <div>
                            <label for="site_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Site Başlığı</label>
                            <input type="text" id="site_title" name="site_title" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="Otel Yönetim Sistemi">
                        </div>
                        
                        <div>
                            <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admin E-posta</label>
                            <input type="email" id="admin_email" name="admin_email" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="admin@example.com">
                        </div>
                        
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zaman Dilimi</label>
                            <select id="timezone" name="timezone" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                <option value="Europe/Istanbul">Europe/Istanbul</option>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New_York</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium mb-3">Görünüm Ayarları</h3>
                    <div class="space-y-3">
                        <div>
                            <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tema</label>
                            <select id="theme" name="theme" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                <option value="light">Açık Tema</option>
                                <option value="dark">Koyu Tema</option>
                                <option value="auto">Otomatik (Sistem)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="accent_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vurgu Rengi</label>
                            <select id="accent_color" name="accent_color" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                <option value="blue">Mavi</option>
                                <option value="indigo">İndigo</option>
                                <option value="purple">Mor</option>
                                <option value="pink">Pembe</option>
                                <option value="red">Kırmızı</option>
                                <option value="orange">Turuncu</option>
                                <option value="yellow">Sarı</option>
                                <option value="green">Yeşil</option>
                                <option value="teal">Turkuaz</option>
                                <option value="cyan">Camgöbeği</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="sidebar_collapsed" name="sidebar_collapsed" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="sidebar_collapsed" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Kenar Çubuğu Daraltılmış Olarak Başlat</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium mb-3">Bildirim Ayarları</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input id="email_notifications" name="email_notifications" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                        <label for="email_notifications" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">E-posta Bildirimleri</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input id="browser_notifications" name="browser_notifications" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                        <label for="browser_notifications" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Tarayıcı Bildirimleri</label>
                    </div>
                    
                    <div>
                        <label for="notification_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bildirim Sıklığı</label>
                        <select id="notification_frequency" name="notification_frequency" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                            <option value="immediately">Anında</option>
                            <option value="hourly">Saatlik Özet</option>
                            <option value="daily">Günlük Özet</option>
                            <option value="weekly">Haftalık Özet</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Varsayılanlara Sıfırla
                </button>
                <button type="button" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Ayarları Kaydet
                </button>
            </div>
        </div>
    </div>
</x-filament::page>