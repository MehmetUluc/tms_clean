<?php

namespace App\Plugins\OTA\Filament\Resources;

use App\Plugins\OTA\Filament\Resources\ChannelResource\Pages;
use App\Plugins\OTA\Filament\Resources\ChannelResource\RelationManagers;
use App\Plugins\OTA\Models\Channel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ChannelResource extends Resource
{
    protected static ?string $model = Channel::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    
    protected static ?string $navigationGroup = 'OTA & Entegrasyonlar';
    
    protected static ?string $navigationLabel = 'OTA Kanalları';
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Kanal Adı')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('Kanal Kodu')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->maxLength(500),
                            
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('ota/logos')
                            ->maxSize(1024)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('150'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Endpoint Ayarları')
                    ->schema([
                        Forms\Components\TextInput::make('settings.import_endpoint')
                            ->label('Import Endpoint')
                            ->helperText('Dış sistemden veri alınacak URL')
                            ->url()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('settings.export_endpoint')
                            ->label('Export Endpoint')
                            ->helperText('Dış sisteme veri gönderilecek URL')
                            ->url()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('credentials.api_key')
                            ->label('API Anahtarı')
                            ->helperText('Dış sistem API anahtarı')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('credentials.api_secret')
                            ->label('API Secret')
                            ->helperText('Dış sistem API şifresi')
                            ->password()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Bağlantı Parametreleri')
                    ->schema([
                        Forms\Components\KeyValue::make('settings.connection_params')
                            ->label('Bağlantı Parametreleri')
                            ->keyLabel('Parametre')
                            ->valueLabel('Değer')
                            ->addActionLabel('Parametre Ekle')
                            ->keyPlaceholder('Örn: timeout')
                            ->valuePlaceholder('Örn: 30')
                            ->helperText('Dış sisteme bağlantı için ihtiyaç duyulan ek parametreler'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular(false)
                    ->height(40),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Kanal Adı')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('slug')
                    ->label('Kod')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('last_sync_at')
                    ->label('Son Senkronizasyon')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
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
                    
                    Tables\Actions\Action::make('sync_now')
                        ->label('Şimdi Senkronize Et')
                        ->icon('heroicon-m-arrow-path')
                        ->color('warning')
                        ->action(function (Channel $record) {
                            // Senkronizasyon işlemi burada yapılacak
                            $record->last_sync_at = now();
                            $record->save();
                        }),
                        
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn (Channel $record) => $record->is_active ? 'Pasif Yap' : 'Aktif Yap')
                        ->icon(fn (Channel $record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                        ->color(fn (Channel $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (Channel $record) {
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
                        ->action(fn (Collection $records) => $records->each(fn (Channel $record) => $record->update(['is_active' => true])))
                        ->color('success')
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Pasif Yap')
                        ->icon('heroicon-m-x-circle')
                        ->action(fn (Collection $records) => $records->each(fn (Channel $record) => $record->update(['is_active' => false])))
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
            'index' => Pages\ListChannels::route('/'),
            'create' => Pages\CreateChannel::route('/create'),
            'edit' => Pages\EditChannel::route('/{record}/edit'),
        ];
    }
}