<?php

namespace App\Plugins\Partner\Filament\Resources;

use App\Plugins\Accommodation\Filament\Resources\HotelResource;
use App\Plugins\Partner\Filament\Resources\PartnerHotelResource\Pages;
use App\Plugins\Partner\Filament\Resources\PartnerHotelResource\RelationManagers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PartnerHotelResource extends HotelResource
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Otellerim';
    
    protected static ?string $navigationGroup = 'Otel Yönetimi';
    
    protected static ?int $navigationSort = 1;
    
    /**
     * Check if the user can access this resource
     */
    public static function canAccess(): bool
    {
        return Auth::check() && 
               Auth::user()->isPartner() && 
               Auth::user()->partner &&
               Auth::user()->partner->onboarding_completed &&
               Auth::user()->can('view_own_hotels');
    }
    
    /**
     * Can create
     */
    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can('create_own_hotels');
    }
    
    /**
     * Can edit
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::check() && 
               Auth::user()->can('update_own_hotels') &&
               $record->partner_id === Auth::user()->partner->id;
    }
    
    /**
     * Can delete
     */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::check() && 
               Auth::user()->can('delete_own_hotels') &&
               $record->partner_id === Auth::user()->partner->id;
    }
    
    /**
     * Can view
     */
    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::check() && 
               Auth::user()->can('view_own_hotels') &&
               $record->partner_id === Auth::user()->partner->id;
    }
    
    /**
     * Apply partner scope to all queries
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $partner = Auth::user()->partner ?? null;
        if ($partner) {
            $query->where('partner_id', $partner->id);
        }
        
        return $query;
    }
    
    /**
     * Override form to set partner_id automatically
     */
    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        $parentForm = parent::form($form);
        $schema = $parentForm->getComponents();
        
        // Add hidden partner_id field
        $partnerField = \Filament\Forms\Components\Hidden::make('partner_id')
            ->default(fn() => Auth::user()->partner?->id)
            ->required();
        
        // Prepend to schema
        array_unshift($schema, $partnerField);
        
        return $parentForm->schema($schema);
    }
    
    /**
     * Get pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerHotels::route('/'),
            'create' => Pages\CreatePartnerHotel::route('/create'),
            'edit' => Pages\EditPartnerHotel::route('/{record}/edit'),
            'view' => Pages\ViewPartnerHotel::route('/{record}'),
        ];
    }
    
    /**
     * Get relation managers - include rooms
     */
    public static function getRelations(): array
    {
        // Get parent relations but override room relation manager if needed
        return [
            RelationManagers\RoomsRelationManager::class,
            // We can add more relation managers here if needed
        ];
    }
    
    /**
     * Get navigation badge
     */
    public static function getNavigationBadge(): ?string
    {
        try {
            $partner = Auth::user()->partner ?? null;
            if (!$partner) {
                return null;
            }
            
            return (string) static::getModel()::where('partner_id', $partner->id)->count();
        } catch (\Exception $e) {
            return null;
        }
    }
}