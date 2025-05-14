<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\XmlSchemaAnalyzer;
use Illuminate\Http\Request;

class SchemaAnalyzerController extends Controller
{
    protected XmlSchemaAnalyzer $analyzer;
    
    public function __construct(XmlSchemaAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }
    
    /**
     * XML veya JSON yapısını analiz et
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'data' => 'required|string',
            'type' => 'required|in:xml,json',
        ]);
        
        $data = $request->input('data');
        $type = $request->input('type');
        
        if ($type === 'xml') {
            $result = $this->analyzer->analyzeFromXml($data);
        } else {
            $result = $this->analyzer->analyzeFromJson($data);
        }
        
        return response()->json($result);
    }
    
    /**
     * Örnek bir kaynak XML analizi
     */
    public function sampleXml()
    {
        $sampleXml = $this->getSampleXml();
        
        $result = $this->analyzer->analyzeFromXml($sampleXml);
        $result['sample'] = $sampleXml;
        
        return response()->json($result);
    }
    
    /**
     * Örnek bir kaynak JSON analizi
     */
    public function sampleJson()
    {
        $sampleJson = $this->getSampleJson();
        
        $result = $this->analyzer->analyzeFromJson($sampleJson);
        $result['sample'] = $sampleJson;
        
        return response()->json($result);
    }
    
    /**
     * Örnek XML veri
     */
    private function getSampleXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0">
  <POS>
    <Source>
      <RequestorID ID="SUPPLIER" Type="1"/>
    </Source>
  </POS>
  <AvailRequestSegments>
    <AvailRequestSegment>
      <HotelSearchCriteria>
        <Criterion>
          <HotelRef HotelCode="12345"/>
          <StayDateRange Start="2025-06-01" End="2025-06-05"/>
          <RoomStayCandidates>
            <RoomStayCandidate Quantity="1">
              <GuestCounts>
                <GuestCount AgeQualifyingCode="10" Count="2"/>
                <GuestCount AgeQualifyingCode="8" Count="1"/>
              </GuestCounts>
            </RoomStayCandidate>
          </RoomStayCandidates>
          <RatePlanCandidates>
            <RatePlanCandidate RatePlanCode="BAR"/>
          </RatePlanCandidates>
        </Criterion>
      </HotelSearchCriteria>
    </AvailRequestSegment>
  </AvailRequestSegments>
</OTA_HotelAvailRQ>
XML;
    }
    
    /**
     * Örnek JSON veri
     */
    private function getSampleJson(): string
    {
        return <<<JSON
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
          },
          {
            "date": "2025-06-02",
            "count": 3,
            "price": 120.50
          }
        ]
      },
      {
        "id": "201",
        "type": "deluxe",
        "availability": [
          {
            "date": "2025-06-01",
            "count": 2,
            "price": 180.00
          },
          {
            "date": "2025-06-02",
            "count": 2,
            "price": 180.00
          }
        ]
      }
    ]
  }
}
JSON;
    }
}