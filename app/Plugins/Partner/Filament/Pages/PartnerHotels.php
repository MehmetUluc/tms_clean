<?php

namespace App\Plugins\Partner\Filament\Pages;

use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Partner\Services\PartnerService;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PartnerHotels extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static string $view = "filament.pages.partner-hotels";
    
    protected static ?string $title = 'My Hotels';
    
    protected static ?string $navigationGroup = "Partner";
    
    protected static ?int $navigationSort = 10;

    public $partner;
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole("partner");
    }
    
    public function mount(): void
    {
        // Redirect if user is not a partner
        if (!auth()->user()->hasRole("partner")) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }

        $partnerService = app(PartnerService::class);
        $this->partner = $partnerService->getPartnerForUser(auth()->user());

        if (!$this->partner) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }
    }
    
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Hotel::query()
            ->where("partner_id", $this->partner->id)
            ->orderBy('name');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Hotel::query()
                    ->where("partner_id", $this->partner->id)
                    ->orderBy('name')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Hotel Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('region.name')
                    ->label('Region')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('star_rating')
                    ->label('Stars')
                    ->formatStateUsing(fn (string $state): string => str_repeat('★', (int) $state))
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('rooms_count')
                    ->label('Rooms')
                    ->counts('rooms')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                
                Tables\Filters\Filter::make('inactive')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', false)),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('View/Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Hotel $record): string => route('filament.admin.resources.hotels.edit', ['record' => $record])),
                
                Tables\Actions\Action::make('manageRooms')
                    ->label('Manage Rooms')
                    ->icon('heroicon-o-home')
                    ->url(fn (Hotel $record): string => route('filament.admin.resources.hotels.edit', ['record' => $record, 'activeTab' => 'rooms'])),
                
                Tables\Actions\Action::make('managePricing')
                    ->label('Manage Pricing')
                    ->icon('heroicon-o-currency-dollar')
                    ->url(fn (Hotel $record): string => route('filament.admin.pages.hotel-pricing-page', ['hotel_id' => $record->id])),
                
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn (Hotel $record): string => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (Hotel $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Hotel $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (Hotel $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                        
                        Notification::make()
                            ->title($record->is_active ? 'Hotel activated' : 'Hotel deactivated')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(function (Hotel $record): void {
                                $record->update(['is_active' => true]);
                            });
                            
                            Notification::make()
                                ->title('Hotels activated')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records): void {
                            $records->each(function (Hotel $record): void {
                                $record->update(['is_active' => false]);
                            });
                            
                            Notification::make()
                                ->title('Hotels deactivated')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('createHotel')
                ->label('Create Hotel')
                ->url(fn () => route('filament.admin.resources.hotels.create', ['partner_id' => $this->partner->id])),
        ];
    }
}