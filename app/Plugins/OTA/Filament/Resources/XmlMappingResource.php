<?php

namespace App\Plugins\OTA\Filament\Resources;

use App\Plugins\OTA\Filament\Resources\XmlMappingResource\Pages;
use App\Plugins\OTA\Models\XmlMapping;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class XmlMappingResource extends Resource
{
    protected static ?string $model = XmlMapping::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    
    protected static ?string $navigationGroup = 'OTA & Entegrasyonlar';
    
    protected static ?string $navigationLabel = 'Veri Eşleştirmeleri';
    
    protected static ?int $navigationSort = 20;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
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
                            
                        Forms\Components\Select::make('direction')
                            ->label('İşlem Tipi')
                            ->options([
                                'import' => 'İçe Aktar (Dış Sistemden İçeri)',
                                'export' => 'Dışa Aktar (Bizden Dış Sisteme)',
                            ])
                            ->required(),

                        Forms\Components\Select::make('format_type')
                            ->label('Format Tipi')
                            ->options([
                                'xml' => 'XML',
                                'json' => 'JSON'
                            ])
                            ->default('xml')
                            ->required(),

                        Forms\Components\Select::make('entity_type')
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
                        Forms\Components\Textarea::make('field_mappings')
                            ->label('Eşleştirme Verisi (JSON)')
                            ->helperText('Eşleştirme kurallarını içeren JSON verisi. Daha detaylı düzenleme için Eşleştirme Düzenleyicisini kullanın.')
                            ->rows(15)
                            ->formatStateUsing(fn ($state) => is_string($state) ? $state : json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                            ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->columnSpan('full'),
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
                    
                Tables\Columns\TextColumn::make('direction')
                    ->label('İşlem')
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

                Tables\Columns\TextColumn::make('entity_type')
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
                    
                Tables\Filters\SelectFilter::make('direction')
                    ->label('İşlem')
                    ->options([
                        'import' => 'İçe Aktar',
                        'export' => 'Dışa Aktar',
                    ]),

                Tables\Filters\SelectFilter::make('format_type')
                    ->label('Format')
                    ->options([
                        'xml' => 'XML',
                        'json' => 'JSON',
                    ]),

                Tables\Filters\SelectFilter::make('entity_type')
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
                        ->url(fn (XmlMapping $record) => static::getUrl('builder', ['record' => $record]))
                        ->color('info'),
                    
                    Tables\Actions\Action::make('test')
                        ->label('Test Et')
                        ->icon('heroicon-m-beaker')
                        ->color('warning')
                        ->action(function (XmlMapping $record) {
                            // Test işlemi burada yapılacak
                        }),
                        
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn (XmlMapping $record) => $record->is_active ? 'Pasif Yap' : 'Aktif Yap')
                        ->icon(fn (XmlMapping $record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                        ->color(fn (XmlMapping $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (XmlMapping $record) {
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
                        ->action(fn (Collection $records) => $records->each(fn (XmlMapping $record) => $record->update(['is_active' => true])))
                        ->color('success')
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Pasif Yap')
                        ->icon('heroicon-m-x-circle')
                        ->action(fn (Collection $records) => $records->each(fn (XmlMapping $record) => $record->update(['is_active' => false])))
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
            'index' => Pages\ListXmlMappings::route('/'),
            'create' => Pages\CreateXmlMapping::route('/create'),
            'edit' => Pages\EditXmlMapping::route('/{record}/edit'),
            'builder' => Pages\XmlMappingBuilder::route('/{record}/builder'),
        ];
    }
}