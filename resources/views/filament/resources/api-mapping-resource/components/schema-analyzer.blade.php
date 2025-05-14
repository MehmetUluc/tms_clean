<div class="space-y-4">
    <div class="mb-4 bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">XML/JSON Yapı Analiz Aracı</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>Karmaşık XML veya JSON verilerini analiz etmek ve alanları otomatik olarak çıkarmak için <a href="{{ url('/xml-mapper') }}" class="font-medium underline" target="_blank">XML/JSON Haritalama Aracını</a> kullanabilirsiniz.</p>
                </div>
            </div>
        </div>
    </div>
    
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        Aşağıda XML ve JSON veri yapılarının nasıl eşleştirileceğine dair örnekler ve referanslar bulabilirsiniz. 
        Soldaki alana XML/JSON içerisindeki alanın adını, sağ tarafa ise veritabanı modelindeki alanın adını girin.
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h3 class="text-lg font-semibold mb-2">Alan Yolları Nasıl Yazılır?</h3>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                XML veya JSON formatında gelen verilerin alanlarına nokta notasyonu kullanarak referans verilir:
            </p>
            
            <div class="mb-4">
                <h4 class="font-medium text-sm mb-2">Erişim Yolları:</h4>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>• <code>parent.child</code> - Basit iç içe alanlar</li>
                    <li>• <code>rooms.*.id</code> - Tüm odaların ID'leri (* joker karakterdir)</li>
                    <li>• <code>parent.@attribute</code> - XML özniteliği (@ ile başlar)</li>
                </ul>
            </div>
        </div>
        
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h3 class="text-lg font-semibold mb-2">Örnek XML Yolları</h3>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                <li>• <code>OTA_HotelAvailRQ.POS.Source.RequestorID</code> → Erişen sistemin kodu</li>
                <li>• <code>OTA_HotelAvailRQ.AvailRequestSegments.AvailRequestSegment.HotelSearchCriteria.Criterion.HotelRef.@HotelCode</code> → Otel kodu özniteliği</li>
                <li>• <code>OTA_HotelAvailRQ.AvailRequestSegments.AvailRequestSegment.HotelSearchCriteria.Criterion.StayDateRange.@Start</code> → Giriş tarihi özniteliği</li>
                <li>• <code>OTA_HotelAvailRQ.AvailRequestSegments.AvailRequestSegment.HotelSearchCriteria.Criterion.GuestCounts.GuestCount.@Count</code> → Misafir sayısı özniteliği</li>
            </ul>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h3 class="text-lg font-semibold mb-2">Örnek JSON Yapısı</h3>
            <pre class="text-xs p-3 bg-gray-100 dark:bg-gray-800 rounded overflow-auto max-h-40">
{
  "hotel": {
    "id": "12345",
    "name": "Grand Hotel",
    "rooms": [
      {
        "id": "101",
        "type": "standard",
        "availability": [
          {
            "date": "2025-06-01",
            "count": 5,
            "price": 120.50
          }
        ]
      }
    ]
  }
}
            </pre>
        </div>
        
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h3 class="text-lg font-semibold mb-2">Örnek JSON Yolları</h3>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                <li>• <code>hotel.id</code> → Otel kodu</li>
                <li>• <code>hotel.name</code> → Otel adı</li>
                <li>• <code>hotel.rooms.*.id</code> → Oda kodları (tüm odalar)</li>
                <li>• <code>hotel.rooms.*.availability.*.date</code> → Tarih</li>
                <li>• <code>hotel.rooms.*.availability.*.count</code> → Müsait oda sayısı</li>
                <li>• <code>hotel.rooms.*.availability.*.price</code> → Oda fiyatı</li>
            </ul>
        </div>
    </div>
    
    <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
        <h3 class="text-lg font-semibold mb-2">Örnek XML Yapısı</h3>
        <pre class="text-xs p-3 bg-gray-100 dark:bg-gray-800 rounded overflow-auto max-h-64">
&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0"&gt;
  &lt;POS&gt;
    &lt;Source&gt;
      &lt;RequestorID ID="SUPPLIER" Type="1"/&gt;
    &lt;/Source&gt;
  &lt;/POS&gt;
  &lt;AvailRequestSegments&gt;
    &lt;AvailRequestSegment&gt;
      &lt;HotelSearchCriteria&gt;
        &lt;Criterion&gt;
          &lt;HotelRef HotelCode="12345"/&gt;
          &lt;StayDateRange Start="2025-06-01" End="2025-06-05"/&gt;
          &lt;RoomStayCandidates&gt;
            &lt;RoomStayCandidate Quantity="1"&gt;
              &lt;GuestCounts&gt;
                &lt;GuestCount AgeQualifyingCode="10" Count="2"/&gt;
                &lt;GuestCount AgeQualifyingCode="8" Count="1"/&gt;
              &lt;/GuestCounts&gt;
            &lt;/RoomStayCandidate&gt;
          &lt;/RoomStayCandidates&gt;
        &lt;/Criterion&gt;
      &lt;/HotelSearchCriteria&gt;
    &lt;/AvailRequestSegment&gt;
  &lt;/AvailRequestSegments&gt;
&lt;/OTA_HotelAvailRQ&gt;
        </pre>
    </div>
</div>