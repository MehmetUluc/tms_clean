<?php

namespace App\Plugins\Partner\Filament\Resources;

use App\Plugins\Partner\Filament\Resources\PartnerStaffResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PartnerStaffResource extends Resource
{
    protected static ?string $model = User::class;
    
    protected static ?string $modelLabel = 'Editör';
    protected static ?string $pluralModelLabel = 'Editörler';
    
    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Editör Yönetimi';
    
    protected static ?string $navigationGroup = 'Ayarlar';
    
    protected static ?int $navigationSort = 1;
    
    /**
     * Can access - only partners with completed onboarding
     */
    public static function canAccess(): bool
    {
        return Auth::check() && 
               Auth::user()->isPartner() && 
               Auth::user()->partner &&
               Auth::user()->partner->onboarding_completed &&
               Auth::user()->can('manage_own_staff');
    }
    
    /**
     * Get eloquent query - only staff members
     */
    public static function getEloquentQuery(): Builder
    {
        $partner = Auth::user()->partner;
        
        if (!$partner) {
            // Return empty query if no partner
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }
        
        // Get staff user IDs from partner
        $staffIds = $partner->staff_user_ids ?? [];
        
        // Add the partner owner to see themselves in the list
        $staffIds[] = $partner->user_id;
        
        return parent::getEloquentQuery()
            ->whereIn('id', $staffIds)
            ->with('roles');
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Editör Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad Soyad')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('password')
                            ->label('Şifre')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText(fn (string $context): string => 
                                $context === 'edit' ? 'Boş bırakırsanız mevcut şifre korunur.' : ''
                            ),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Pasif editörler sisteme giriş yapamaz.'),
                            
                        Forms\Components\Select::make('role')
                            ->label('Yetki')
                            ->options([
                                'partner' => 'Partner Yöneticisi',
                                'partner_staff' => 'Partner Editörü',
                            ])
                            ->default('partner_staff')
                            ->required()
                            ->disabled(fn ($record) => $record && $record->id === Auth::user()->partner->user_id)
                            ->helperText('Partner Yöneticisi tüm yetkilere sahiptir. Editörler sınırlı yetkiye sahiptir.'),
                    ]),
                    
                Forms\Components\Section::make('İletişim Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(20),
                            
                        Forms\Components\TextInput::make('mobile')
                            ->label('Mobil Telefon')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->collapsed(),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('role')
                    ->label('Yetki')
                    ->getStateUsing(function ($record) {
                        if ($record->hasRole('partner')) {
                            return 'Partner Yöneticisi';
                        } elseif ($record->hasRole('partner_staff')) {
                            return 'Partner Editörü';
                        }
                        return '-';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Partner Yöneticisi' => 'success',
                        'Partner Editörü' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Eklenme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Son Giriş')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Henüz giriş yapmadı'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Yetki')
                    ->options([
                        'partner' => 'Partner Yöneticisi',
                        'partner_staff' => 'Partner Editörü',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->whereHas('roles', function ($q) use ($data) {
                                $q->where('name', $data['value']);
                            });
                        }
                        return $query;
                    }),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, $record) {
                        // Prevent deleting the partner owner
                        if ($record->id === Auth::user()->partner->user_id) {
                            $action->cancel();
                            \Filament\Notifications\Notification::make()
                                ->title('İşlem Başarısız')
                                ->body('Partner yöneticisi silinemez.')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Tables\Actions\DeleteBulkAction $action, $records) {
                            // Remove partner owner from bulk delete
                            $partnerId = Auth::user()->partner->user_id;
                            $records = $records->filter(fn ($record) => $record->id !== $partnerId);
                            
                            if ($records->isEmpty()) {
                                $action->cancel();
                                \Filament\Notifications\Notification::make()
                                    ->title('İşlem Başarısız')
                                    ->body('Seçilen kullanıcılar silinemez.')
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
    
    /**
     * Get pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerStaff::route('/'),
            'create' => Pages\CreatePartnerStaff::route('/create'),
            'edit' => Pages\EditPartnerStaff::route('/{record}/edit'),
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
            
            // Count staff members (excluding the partner owner)
            $staffCount = count($partner->staff_user_ids ?? []);
            
            return $staffCount > 0 ? (string) $staffCount : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}