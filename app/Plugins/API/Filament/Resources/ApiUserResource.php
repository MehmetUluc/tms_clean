<?php

namespace App\Plugins\API\Filament\Resources;

use App\Plugins\API\Filament\Resources\ApiUserResource\Pages;
use App\Plugins\API\Filament\Resources\ApiUserResource\RelationManagers;
use App\Plugins\API\Models\ApiUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ApiUserResource extends Resource
{
    protected static ?string $model = ApiUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    
    protected static ?string $navigationGroup = 'API & Entegrasyonlar';
    
    protected static ?string $navigationLabel = 'API Kullanıcıları';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kullanıcı Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('username')
                            ->label('Kullanıcı Adı')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                            
                        Forms\Components\TextInput::make('password')
                            ->label('Şifre')
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => $state)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                            
                        Forms\Components\TextInput::make('company_name')
                            ->label('Şirket Adı')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('contact_email')
                            ->label('İletişim E-posta')
                            ->email()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('İletişim Telefon')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('API Erişim Bilgileri')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('api_key')
                                    ->label('API Anahtarı')
                                    ->placeholder('Otomatik oluşturulacak')
                                    ->helperText('Bu alanı boş bırakırsanız otomatik oluşturulacak')
                                    ->maxLength(64),
                                
                                Forms\Components\Button::make('generate_api_key')
                                    ->label('Yeni API Anahtarı Oluştur')
                                    ->action(function ($livewire, $set) {
                                        $apiKey = \Illuminate\Support\Str::random(64);
                                        $set('api_key', $apiKey);
                                    }),
                            ]),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->maxLength(500),
                    ]),
                
                Forms\Components\Section::make('Güvenlik Ayarları')
                    ->schema([
                        Forms\Components\TagsInput::make('allowed_ips')
                            ->label('İzin Verilen IP Adresleri')
                            ->placeholder('Yeni IP ekle...')
                            ->helperText('Boş bırakırsanız tüm IP adreslerine izin verilir. * değeri tüm IP adreslerine izin verir.')
                            ->splitKeys(['Tab', ',', ' ']),
                            
                        Forms\Components\Select::make('permissions')
                            ->label('İzinler')
                            ->options([
                                'read' => 'Okuma',
                                'write' => 'Yazma',
                                'delete' => 'Silme',
                                'inventory' => 'Envanter Yönetimi',
                                'pricing' => 'Fiyat Yönetimi',
                                'analytics' => 'Analitikler',
                                '*' => 'Tüm İzinler',
                            ])
                            ->helperText('Kullanıcının yapabileceği işlemler')
                            ->multiple()
                            ->default(['read']),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('username')
                    ->label('Kullanıcı Adı')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Şirket')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('contact_email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Son Aktivite')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->trueLabel('Sadece Aktif')
                    ->falseLabel('Sadece Pasif')
                    ->placeholder('Tümü'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('view_api_info')
                        ->label('API Bilgilerini Görüntüle')
                        ->icon('heroicon-m-key')
                        ->color('info')
                        ->modalContent(fn (ApiUser $record) => view('filament.resources.api-user-resource.components.api-credentials', ['record' => $record])),
                        
                    Tables\Actions\Action::make('regenerate_api_key')
                        ->label('API Anahtarını Yenile')
                        ->icon('heroicon-m-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (ApiUser $record) {
                            $record->generateApiKey();
                            $record->save();
                        }),
                        
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn (ApiUser $record) => $record->is_active ? 'Pasif Yap' : 'Aktif Yap')
                        ->icon(fn (ApiUser $record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                        ->color(fn (ApiUser $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (ApiUser $record) {
                            $record->is_active = !$record->is_active;
                            $record->save();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktif Yap')
                        ->icon('heroicon-m-check-circle')
                        ->action(fn (Collection $records) => $records->each(fn (ApiUser $record) => $record->update(['is_active' => true])))
                        ->color('success')
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Pasif Yap')
                        ->icon('heroicon-m-x-circle')
                        ->action(fn (Collection $records) => $records->each(fn (ApiUser $record) => $record->update(['is_active' => false])))
                        ->color('danger')
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MappingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiUsers::route('/'),
            'create' => Pages\CreateApiUser::route('/create'),
            'edit' => Pages\EditApiUser::route('/{record}/edit'),
        ];
    }
}