<?php

namespace App\Plugins\Partner\Filament\Resources;

use App\Plugins\Booking\Filament\Resources\ReservationResource;
use App\Plugins\Partner\Filament\Resources\PartnerReservationResource\Pages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Plugins\Accommodation\Models\Hotel;

class PartnerReservationResource extends ReservationResource
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    
    protected static ?string $navigationLabel = 'Rezervasyonlar';
    
    protected static ?string $navigationGroup = 'Rezervasyonlar';
    
    protected static ?int $navigationSort = 1;
    
    /**
     * Check if the user can access this resource
     */
    public static function canAccess(): bool
    {
        return Auth::check() && 
               Auth::user()->isPartner() && 
               Auth::user()->getAssociatedPartner() &&
               Auth::user()->getAssociatedPartner()->onboarding_completed &&
               Auth::user()->can('view_own_reservations');
    }
    
    /**
     * Can edit
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::check() && 
               Auth::user()->can('update_own_reservations');
    }
    
    /**
     * Can view
     */
    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::check() && 
               Auth::user()->can('view_own_reservations');
    }
    
    /**
     * Apply partner scope to all queries
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $partner = Auth::user()->getAssociatedPartner() ?? null;
        if ($partner) {
            // Get partner's hotel IDs
            $hotelIds = Hotel::where('partner_id', $partner->id)->pluck('id');
            
            // Filter reservations by partner's hotels
            $query->whereIn('hotel_id', $hotelIds);
        }
        
        return $query;
    }
    
    /**
     * Override form to limit hotel selection to partner's hotels
     */
    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        $parentForm = parent::form($form);
        
        // Get the schema
        $schema = $parentForm->getComponents();
        
        // Find and modify the hotel select field
        foreach ($schema as $component) {
            if ($component instanceof \Filament\Forms\Components\Tabs) {
                foreach ($component->getChildComponents() as $tab) {
                    foreach ($tab->getChildComponents() as $field) {
                        if ($field instanceof \Filament\Forms\Components\Select && 
                            $field->getName() === 'hotel_id') {
                            
                            // Override to show only partner's hotels
                            $field->options(function() {
                                $partner = Auth::user()->getAssociatedPartner() ?? null;
                                if (!$partner) {
                                    return [];
                                }
                                
                                return Hotel::where('partner_id', $partner->id)
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->afterStateUpdated(function (callable $set, $state) {
                                // Verify hotel belongs to partner
                                if ($state) {
                                    $partner = Auth::user()->getAssociatedPartner() ?? null;
                                    if ($partner) {
                                        $hotelBelongsToPartner = Hotel::where('id', $state)
                                            ->where('partner_id', $partner->id)
                                            ->exists();
                                        
                                        if (!$hotelBelongsToPartner) {
                                            $set('hotel_id', null);
                                            $set('room_id', null);
                                        }
                                    }
                                }
                                
                                // Reset room selection
                                $set('room_id', null);
                            });
                        }
                    }
                }
            }
        }
        
        return $parentForm;
    }
    
    /**
     * Can create - partners cannot create reservations directly
     */
    public static function canCreate(): bool
    {
        return false;
    }
    
    /**
     * Can delete - partners cannot delete reservations
     */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
    
    /**
     * Can force delete - partners cannot force delete
     */
    public static function canForceDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
    
    /**
     * Can restore - partners cannot restore
     */
    public static function canRestore(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
    
    /**
     * Get pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerReservations::route('/'),
            'edit' => Pages\EditPartnerReservation::route('/{record}/edit'),
            'view' => Pages\ViewPartnerReservation::route('/{record}'),
        ];
    }
    
    /**
     * Get navigation badge - show count of partner's reservations
     */
    public static function getNavigationBadge(): ?string
    {
        try {
            $partner = Auth::user()->getAssociatedPartner() ?? null;
            if (!$partner) {
                return null;
            }
            
            $hotelIds = Hotel::where('partner_id', $partner->id)->pluck('id');
            
            return (string) static::getModel()::whereIn('hotel_id', $hotelIds)
                ->whereIn('status', ['confirmed', 'pending'])
                ->count();
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get navigation badge color
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}