<div class="p-4 space-y-4">
    <h2 class="text-xl font-bold">XML/JSON Formatları Hakkında Bilgi</h2>
    
    <div class="grid grid-cols-1 gap-4">
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h3 class="text-lg font-semibold mb-2">Alan Yolları Nasıl Yazılır?</h3>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                XML veya JSON formatında gelen verilerin alanlarına nokta notasyonu kullanarak referans verilir:
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-medium text-sm mb-2">XML Yapısı:</h4>
                    <pre class="text-xs p-3 bg-gray-100 dark:bg-gray-800 rounded">
&lt;root&gt;
  &lt;parent&gt;
    &lt;child&gt;değer&lt;/child&gt;
    &lt;child id="1"&gt;başka değer&lt;/child&gt;
  &lt;/parent&gt;
&lt;/root&gt;
                    </pre>
                </div>
                <div>
                    <h4 class="font-medium text-sm mb-2">Erişim Yolları:</h4>
                    <ul class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                        <li>• <code>root.parent.child</code> - İlk child elemanına</li>
                        <li>• <code>root.parent.child[1]</code> - İkinci child elemanına</li>
                        <li>• <code>root.parent.child.@id</code> - "id" özniteliğine (@{attribute_name} şeklinde)</li>
                    </ul>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <h4 class="font-medium text-sm mb-2">JSON Yapısı:</h4>
                    <pre class="text-xs p-3 bg-gray-100 dark:bg-gray-800 rounded">
{
  "parent": {
    "children": [
      { "id": 1, "name": "Child 1" },
      { "id": 2, "name": "Child 2" }
    ],
    "status": "active"
  }
}
                    </pre>
                </div>
                <div>
                    <h4 class="font-medium text-sm mb-2">Erişim Yolları:</h4>
                    <ul class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                        <li>• <code>parent.status</code> - Status alanına</li>
                        <li>• <code>parent.children.0.name</code> - İlk child'ın adına (0-indexed)</li>
                        <li>• <code>parent.children.*.id</code> - Tüm child'ların id'lerine (array)</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h3 class="text-lg font-semibold mb-2">Dizi İçindeki Öğelere Erişim</h3>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                XML veya JSON dizilerindeki öğelere nasıl erişileceğini açıklayan örnekler:
            </p>
            
            <div class="mb-4">
                <h4 class="font-medium text-sm mb-2">İndeks ile Erişim:</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    Dizinin belirli bir elemanına erişmek için indeks kullanabilirsiniz. JSON'da indeksler 0'dan, XML'de 1'den başlar.
                </p>
                <pre class="text-xs p-3 bg-gray-100 dark:bg-gray-800 rounded">
<b>JSON:</b> parent.children.0.name  <span class="text-green-600">// İlk eleman (0-indexed)</span>
<b>XML:</b>  parent.child[1].name    <span class="text-green-600">// İlk eleman (1-indexed)</span>
                </pre>
            </div>
            
            <div>
                <h4 class="font-medium text-sm mb-2">Yıldız Operatörü:</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    Dizinin tüm elemanlarına erişmek için yıldız (*) operatörünü kullanabilirsiniz. Bu, özellikle yinelenen öğeler için kullanışlıdır.
                </p>
                <pre class="text-xs p-3 bg-gray-100 dark:bg-gray-800 rounded">
<b>JSON:</b> rooms.*.availability.*.date  <span class="text-green-600">// Tüm odaların, tüm uygunluk tarihlerini alır</span>
<b>XML:</b>  RoomList.Room.*.Availability.*.Date
                </pre>
            </div>
        </div>
        
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h3 class="text-lg font-semibold mb-2">Özel Veri Dönüşümleri</h3>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                Bazı durumlarda, alınan veriyi dönüştürmeniz gerekebilir. Örneğin tarih formatlarını değiştirme, sayısal değerleri işleme alma gibi.
            </p>
            
            <div class="mb-4">
                <h4 class="font-medium text-sm mb-2">Yaygın Dönüşüm Örnekleri:</h4>
                <ul class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                    <li>• <code>hotel.rooms.*.availability.*.date</code> → <code>date</code> (Tarih formatını dönüştürme)</li>
                    <li>• <code>hotel.rooms.*.availability.*.price</code> → <code>price</code> (Para birimi işleme)</li>
                    <li>• <code>OTA_HotelAvailRS.RoomStays.RoomStay.*.BasicPropertyInfo.@HotelCode</code> → <code>hotel_id</code> (XML özniteliğini alma)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
