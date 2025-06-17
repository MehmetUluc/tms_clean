<?php

namespace App\Plugins\Partner\Filament\Resources;

use App\Plugins\Accommodation\Filament\Resources\RoomResource;
use App\Plugins\Partner\Filament\Resources\PartnerRoomResource\Pages;
use App\Plugins\Accommodation\Models\Hotel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;

class PartnerRoomResource extends RoomResource
{
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    
    protected static ?string $navigationLabel = 'Odalarım';
    
    protected static ?string $navigationGroup = 'Otel Yönetimi';
    
    protected static ?int $navigationSort = 2;
    
    /**
     * Check if the user can access this resource
     */
    public static function canAccess(): bool
    {
        return Auth::check() && 
               Auth::user()->isPartner() && 
               Auth::user()->getAssociatedPartner() &&
               Auth::user()->getAssociatedPartner()->onboarding_completed &&
               Auth::user()->can('view_own_rooms');
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
            
            // Filter rooms by partner's hotels
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
        $schema = $parentForm->getComponents();
        
        // Find and modify the hotel select field
        foreach ($schema as $component) {
            if ($component instanceof \Filament\Forms\Components\Tabs) {
                foreach ($component->getChildComponents() as $tab) {
                    foreach ($tab->getChildComponents() as $section) {
                        if ($section instanceof \Filament\Forms\Components\Section) {
                            foreach ($section->getChildComponents() as $field) {
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
                                    ->afterStateUpdated(function ($state) {
                                        // Verify hotel belongs to partner
                                        if ($state) {
                                            $partner = Auth::user()->getAssociatedPartner() ?? null;
                                            if ($partner) {
                                                $hotelBelongsToPartner = Hotel::where('id', $state)
                                                    ->where('partner_id', $partner->id)
                                                    ->exists();
                                                
                                                if (!$hotelBelongsToPartner) {
                                                    return null;
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $parentForm;
    }
    
    /**
     * Can create
     */
    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can('create_own_rooms');
    }
    
    /**
     * Can edit
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $partner = Auth::user()->getAssociatedPartner() ?? null;
        if (!$partner) {
            return false;
        }
        
        // Check if room's hotel belongs to partner
        $hotelBelongsToPartner = Hotel::where('id', $record->hotel_id)
            ->where('partner_id', $partner->id)
            ->exists();
        
        return Auth::user()->can('update_own_rooms') && $hotelBelongsToPartner;
    }
    
    /**
     * Can delete
     */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $partner = Auth::user()->getAssociatedPartner() ?? null;
        if (!$partner) {
            return false;
        }
        
        // Check if room's hotel belongs to partner
        $hotelBelongsToPartner = Hotel::where('id', $record->hotel_id)
            ->where('partner_id', $partner->id)
            ->exists();
        
        return Auth::user()->can('delete_own_rooms') && $hotelBelongsToPartner;
    }
    
    /**
     * Can view
     */
    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $partner = Auth::user()->getAssociatedPartner() ?? null;
        if (!$partner) {
            return false;
        }
        
        // Check if room's hotel belongs to partner
        $hotelBelongsToPartner = Hotel::where('id', $record->hotel_id)
            ->where('partner_id', $partner->id)
            ->exists();
        
        return Auth::user()->can('view_own_rooms') && $hotelBelongsToPartner;
    }
    
    /**
     * Get pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerRooms::route('/'),
            'create' => Pages\CreatePartnerRoom::route('/create'),
            'edit' => Pages\EditPartnerRoom::route('/{record}/edit'),
        ];
    }
    
    /**
     * Get navigation badge
     */
    public static function getNavigationBadge(): ?string
    {
        try {
            $partner = Auth::user()->getAssociatedPartner() ?? null;
            if (!$partner) {
                return null;
            }
            
            $hotelIds = Hotel::where('partner_id', $partner->id)->pluck('id');
            
            return (string) static::getModel()::whereIn('hotel_id', $hotelIds)->count();
        } catch (\Exception $e) {
            return null;
        }
    }
}