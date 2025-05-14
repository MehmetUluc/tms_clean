<?php

namespace App\Plugins\OTA\Filament\Resources;

use App\Plugins\OTA\Filament\Resources\DataMappingResource\Pages;
use App\Plugins\OTA\Models\DataMapping;
use App\Plugins\OTA\Services\DataMappingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class DataMappingResource extends Resource
{
    protected static ?string $model = DataMapping::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    
    protected static ?string $navigationGroup = 'OTA & Entegrasyonlar';
    
    protected static ?string $navigationLabel = 'Veri Eşleştirmeleri';
    
    protected static ?int $navigationSort = 20;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        $dataMappingService = app(DataMappingService::class);
        
        return $form
            ->schema([
                Forms\Components\Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Eşleştirme Adı')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('channel_id')
                            ->label('OTA Kanalı')
                            ->relationship('channel', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('operation_type')
                            ->label('İşlem Tipi')
                            ->options($dataMappingService->getAvailableOperations())
                            ->required(),
                            
                        Forms\Components\Select::make('format_type')
                            ->label('Format Tipi')
                            ->options($dataMappingService->getAvailableFormats())
                            ->required(),
                            
                        Forms\Components\Select::make('mapping_entity')
                            ->label('Eşleştirme Varlığı')
                            ->options([
                                'room' => 'Oda',
                                'rate' => 'Fiyat',
                                'availability' => 'Müsaitlik',
                                'reservation' => 'Rezervasyon',
                                'hotel' => 'Otel',
                                'guest' => 'Misafir',
                            ])
                            ->required(),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->maxLength(500),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Eşleştirme Verisi')
                    ->schema([
                        Forms\Components\Textarea::make('mapping_data')
                            ->label('Eşleştirme Verisi (JSON)')
                            ->helperText('Eşleştirme kurallarını içeren JSON verisi. Daha detaylı düzenleme için Eşleştirme Düzenleyicisini kullanın.')
                            ->rows(15)
                            ->formatStateUsing(fn ($state) => is_string($state) ? $state : json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                            ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->columnSpan('full'),
                    ]),
                    
                Forms\Components\Section::make('Şablon İçeriği')
                    ->schema([
                        Forms\Components\Textarea::make('template_content')
                            ->label('Şablon İçeriği')
                            ->helperText('Dışa aktarım (export) işlemleri için şablon. Şablon değişkenleri {{değişken}} şeklinde kullanılabilir.')
                            ->rows(10)
                            ->columnSpan('full')
                            ->visible(fn (Forms\Get $get) => $get('operation_type') === 'export'),
                            
                        Forms\Components\TextInput::make('template_format')
                            ->label('Şablon Formatı')
                            ->helperText('Örn: json, xml, csv')
                            ->visible(fn (Forms\Get $get) => $get('operation_type') === 'export'),
                            
                        Forms\Components\TextInput::make('version')
                            ->label('Versiyon')
                            ->default('1.0')
                            ->visible(fn (Forms\Get $get) => $get('operation_type') === 'export'),
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
                    
                Tables\Columns\TextColumn::make('channel.name')
                    ->label('OTA Kanalı')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('operation_type')
                    ->label('İşlem Tipi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'import' => 'success',
                        'export' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'import' => 'İçe Aktar',
                        'export' => 'Dışa Aktar',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('format_type')
                    ->label('Format')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'xml' => 'warning',
                        'json' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                    
                Tables\Columns\TextColumn::make('mapping_entity')
                    ->label('Varlık')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'room' => 'Oda',
                        'rate' => 'Fiyat',
                        'availability' => 'Müsaitlik',
                        'reservation' => 'Rezervasyon',
                        'hotel' => 'Otel',
                        'guest' => 'Misafir',
                        default => $state,
                    }),
                    
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel_id')
                    ->label('OTA Kanalı')
                    ->relationship('channel', 'name'),
                    
                Tables\Filters\SelectFilter::make('operation_type')
                    ->label('İşlem Tipi')
                    ->options([
                        'import' => 'İçe Aktar',
                        'export' => 'Dışa Aktar',
                    ]),
                    
                Tables\Filters\SelectFilter::make('format_type')
                    ->label('Format Tipi')
                    ->options([
                        'xml' => 'XML',
                        'json' => 'JSON',
                    ]),
                    
                Tables\Filters\SelectFilter::make('mapping_entity')
                    ->label('Varlık')
                    ->options([
                        'room' => 'Oda',
                        'rate' => 'Fiyat',
                        'availability' => 'Müsaitlik',
                        'reservation' => 'Rezervasyon',
                        'hotel' => 'Otel',
                        'guest' => 'Misafir',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->trueLabel('Sadece Aktif')
                    ->falseLabel('Sadece Pasif')
                    ->placeholder('Tümü'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('edit_mapping')
                        ->label('Eşleştirme Düzenleyici')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (DataMapping $record) => static::getUrl('builder', ['record' => $record]))
                        ->color('info'),
                    
                    Tables\Actions\Action::make('test')
                        ->label('Test Et')
                        ->icon('heroicon-m-beaker')
                        ->color('warning')
                        ->action(function (DataMapping $record) {
                            // Testing functionality will be implemented here
                        }),
                        
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn (DataMapping $record) => $record->is_active ? 'Pasif Yap' : 'Aktif Yap')
                        ->icon(fn (DataMapping $record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                        ->color(fn (DataMapping $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (DataMapping $record) {
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
                        ->action(fn (Collection $records) => $records->each(fn (DataMapping $record) => $record->update(['is_active' => true])))
                        ->color('success')
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Pasif Yap')
                        ->icon('heroicon-m-x-circle')
                        ->action(fn (Collection $records) => $records->each(fn (DataMapping $record) => $record->update(['is_active' => false])))
                        ->color('danger')
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataMappings::route('/'),
            'create' => Pages\CreateDataMapping::route('/create'),
            'edit' => Pages\EditDataMapping::route('/{record}/edit'),
            'builder' => Pages\DataMappingBuilder::route('/{record}/builder'),
        ];
    }
}