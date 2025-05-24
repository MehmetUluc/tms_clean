@props([
    'hotel' => null,
    'selectedRooms' => [],
    'checkIn' => null,
    'checkOut' => null,
    'nights' => 0,
    'adults' => 0,
    'children' => 0,
    'childrenAges' => [],
    'subtotal' => 0,
    'airportTransferPrice' => 0,
    'travelInsurancePrice' => 0,
    'discount' => 0,
    'taxes' => 0,
    'totalPrice' => 0,
    'guest' => [],
    'specialRequests' => null
])

<div class="summary-section">
    @if($hotel)
        @include('booking::components.booking-wizard.accommodation-details', [
            'hotel' => $hotel,
            'selectedRooms' => $selectedRooms,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'nights' => $nights,
            'adults' => $adults,
            'children' => $children
        ])
    @endif
    
    @include('booking::components.booking-wizard.price-breakdown', [
        'selectedRooms' => $selectedRooms,
        'subtotal' => $subtotal,
        'children' => $children,
        'childrenAges' => $childrenAges,
        'airportTransferPrice' => $airportTransferPrice,
        'travelInsurancePrice' => $travelInsurancePrice,
        'discount' => $discount,
        'taxes' => $taxes,
        'totalPrice' => $totalPrice
    ])
    
    @include('booking::components.booking-wizard.guest-info-summary', [
        'guest' => $guest,
        'specialRequests' => $specialRequests
    ])
</div>