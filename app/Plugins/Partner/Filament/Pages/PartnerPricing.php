<?php

namespace App\Plugins\Partner\Filament\Pages;

use App\Plugins\Pricing\Filament\Pages\HotelPricingPage;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\NavigationItem;
use App\Plugins\Accommodation\Models\Hotel;

class PartnerPricing extends HotelPricingPage
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static string $view = 'filament.pages.hotel-pricing-page';
    
    protected static ?string $navigationLabel = 'Fiyat Yönetimi';
    
    protected static ?string $title = 'Fiyat Yönetimi';
    
    protected static ?int $navigationSort = 2;
    
    /**
     * Check if the user can access this page
     */
    public static function canAccess(): bool
    {
        return Auth::check() && 
               Auth::user()->isPartner() && 
               Auth::user()->partner &&
               Auth::user()->partner->onboarding_completed &&
               Auth::user()->can('view_own_pricing');
    }
    
    /**
     * Get navigation items - partner için özelleştirilmiş
     */
    public static function getNavigationItems(): array
    {
        if (!static::canAccess()) {
            return [];
        }

        return [
            NavigationItem::make(static::getNavigationLabel())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteName()))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge())
                ->url(static::getNavigationUrl()),
        ];
    }
    
    /**
     * Override form to limit hotel selection to partner's hotels only
     */
    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        $parentForm = parent::form($form);
        
        // Get the schema and modify the hotel select field
        $schema = $parentForm->getComponents();
        
        // Find and modify the hotel select component
        foreach ($schema as $section) {
            if ($section instanceof \Filament\Forms\Components\Section) {
                foreach ($section->getChildComponents() as $grid) {
                    if ($grid instanceof \Filament\Forms\Components\Grid) {
                        foreach ($grid->getChildComponents() as $field) {
                            if ($field instanceof \Filament\Forms\Components\Select && 
                                $field->getName() === 'formSelectedHotelId') {
                                
                                // Override options to show only partner's hotels
                                $field->options(function() {
                                    $partner = Auth::user()->partner;
                                    if (!$partner) {
                                        return [];
                                    }
                                    
                                    return Hotel::where('partner_id', $partner->id)
                                        ->where('is_active', true)
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->getSearchResultsUsing(function (string $search) {
                                    $partner = Auth::user()->partner;
                                    if (!$partner) {
                                        return [];
                                    }
                                    
                                    return Hotel::where('partner_id', $partner->id)
                                        ->where('is_active', true)
                                        ->where('name', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->placeholder('Otel seçiniz...')
                                ->helperText('Sadece sizin otelleriniz listelenmektedir.');
                            }
                        }
                    }
                }
            }
        }
        
        return $parentForm;
    }
    
    /**
     * Mount with partner scope
     */
    public function mount($hotel = null, $hotel_id = null): void
    {
        // If hotel_id is provided, verify it belongs to the partner
        if ($hotel_id || $hotel) {
            $hotelIdToCheck = $hotel_id ?: $hotel;
            $partner = Auth::user()->partner;
            
            if ($partner) {
                $hotelBelongsToPartner = Hotel::where('id', $hotelIdToCheck)
                    ->where('partner_id', $partner->id)
                    ->exists();
                
                if (!$hotelBelongsToPartner) {
                    // Hotel doesn't belong to partner, reset parameters
                    $hotel = null;
                    $hotel_id = null;
                }
            }
        }
        
        parent::mount($hotel, $hotel_id);
    }
    
    /**
     * Get navigation badge - sadece partner'a ait rate plan sayısı
     */
    public static function getNavigationBadge(): ?string
    {
        try {
            $partner = Auth::user()->partner ?? null;
            if (!$partner) {
                return null;
            }
            
            // Partner'a ait otellerin rate plan sayısını hesapla
            $hotelIds = Hotel::where('partner_id', $partner->id)->pluck('id');
            
            return (string) \App\Plugins\Pricing\Models\RatePlan::whereIn('hotel_id', $hotelIds)->count();
        } catch (\Exception $e) {
            return null;
        }
    }
}