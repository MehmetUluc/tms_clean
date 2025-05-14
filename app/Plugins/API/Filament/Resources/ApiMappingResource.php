<?php

namespace App\Plugins\API\Filament\Resources;

use App\Plugins\API\Filament\Resources\ApiMappingResource\Pages;
use App\Plugins\API\Models\ApiMapping;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\RoomInventory;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Pricing\Models\RoomRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ApiMappingResource extends Resource
{
    protected static ?string $model = ApiMapping::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'API & Entegrasyonlar';
    
    protected static ?string $navigationLabel = 'XML/JSON Mapping';
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Mapping Adı')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('api_user_id')
                            ->label('API Kullanıcısı')
                            ->relationship('apiUser', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\TextInput::make('endpoint_path')
                            ->label('API Endpoint Yolu')
                            ->helperText('Dış sistem bu yolu kullanarak veri gönderecek (/api/v1/...). Boş bırakırsanız otomatik oluşturulacak.')
                            ->prefix(fn () => config('app.url') . '/api/v1/')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->maxLength(500),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
                    
                Forms\Components\Tabs::make('Settings')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'vertical-tabs'])
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Kaynak Ayarları')
                            ->schema([
                                Forms\Components\Select::make('source_type')
                                    ->label('Veri Kaynağı Türü')
                                    ->options([
                                        'xml' => 'XML',
                                        'json' => 'JSON',
                                        'csv' => 'CSV',
                                    ])
                                    ->required()
                                    ->default('xml'),
                                    
                                Forms\Components\Select::make('frequency')
                                    ->label('Senkronizasyon Sıklığı')
                                    ->options([
                                        'on_demand' => 'İstek Üzerine',
                                        'hourly' => 'Saatlik',
                                        'daily' => 'Günlük',
                                        'weekly' => 'Haftalık',
                                    ])
                                    ->default('on_demand')
                                    ->required(),
                                    
                                Forms\Components\KeyValue::make('validation_rules')
                                    ->label('Doğrulama Kuralları')
                                    ->keyLabel('Alan')
                                    ->valueLabel('Kurallar')
                                    ->keyPlaceholder('Örn: room_id')
                                    ->valuePlaceholder('Örn: required|numeric')
                                    ->helperText('Gelen verilerin doğrulanması için Laravel validation kuralları'),
                                    
                                Forms\Components\Textarea::make('schema')
                                    ->label('Şema (JSON Format)')
                                    ->helperText('Beklenen veri şemasını JSON formatında tanımlayın')
                                    ->rows(10)
                                    ->columnSpan('full'),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Hedef Ayarları')
                            ->schema([
                                Forms\Components\Select::make('target_model')
                                    ->label('Hedef Model')
                                    ->options([
                                        Room::class => 'Oda',
                                        RoomInventory::class => 'Oda Envanteri',
                                        RoomRate::class => 'Oda Fiyatı',
                                        Hotel::class => 'Otel',
                                        // Diğer modeller burada listelenebilir
                                    ])
                                    ->required(),
                                    
                                Forms\Components\Select::make('target_operation')
                                    ->label('İşlem Türü')
                                    ->options([
                                        'create' => 'Yeni Kayıt Oluştur',
                                        'update' => 'Mevcut Kaydı Güncelle',
                                        'upsert' => 'Varsa Güncelle, Yoksa Oluştur',
                                        'delete' => 'Kaydı Sil',
                                        'custom' => 'Özel İşlem',
                                    ])
                                    ->default('update')
                                    ->required(),
                                    
                                Forms\Components\TextInput::make('pre_processor_class')
                                    ->label('Ön İşleme Sınıfı')
                                    ->helperText('Veriyi işlemeden önce çalıştırılacak sınıf (opsiyonel)')
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('post_processor_class')
                                    ->label('Son İşleme Sınıfı')
                                    ->helperText('Veriyi işledikten sonra çalıştırılacak sınıf (opsiyonel)')
                                    ->maxLength(255),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Alan Eşleştirme')
                            ->schema([
                                Forms\Components\KeyValue::make('field_mappings')
                                    ->label('Alan Eşleştirmeleri')
                                    ->keyLabel('Kaynak Alan')
                                    ->valueLabel('Hedef Alan')
                                    ->keyPlaceholder('Örn: Room.Id veya id')
                                    ->valuePlaceholder('Örn: external_id veya price')
                                    ->required()
                                    ->columnSpan('full')
                                    ->afterStateHydrated(function ($component, $state) {
                                        $component->state($state ?? []);
                                    })
                                    ->extraAttributes([
                                        'x-init' => '
                                            // XML Mapper aracından gelen alan eşleştirmelerini yükle
                                            if (typeof localStorage !== "undefined") {
                                                const mappings = localStorage.getItem("xml_mapper_field_mappings");
                                                if (mappings && !$wire.mountedTableActions.length && !$wire.mountedTableBulkActions.length) {
                                                    try {
                                                        const mappingData = JSON.parse(mappings);
                                                        if (Object.keys(mappingData).length > 0) {
                                                            setTimeout(() => {
                                                                $el.dispatchEvent(new CustomEvent("set-mappings", { 
                                                                    detail: { mappings: mappingData } 
                                                                }));
                                                                // Bir kere kullandıktan sonra localStorage\'dan temizle
                                                                localStorage.removeItem("xml_mapper_field_mappings");
                                                            }, 500);
                                                        }
                                                    } catch (e) {
                                                        console.error("XML Mapper verisi yüklenemedi", e);
                                                    }
                                                }
                                            }
                                        ',
                                        'x-on:set-mappings.window' => '
                                            if ($event.detail && $event.detail.mappings) {
                                                $wire.set("data.field_mappings", $event.detail.mappings);
                                            }
                                        '
                                    ]),
                                    
                                Forms\Components\Placeholder::make('schema_help')
                                    ->label('XML/JSON Eşleştirme Yardımı')
                                    ->content(function () {
                                        return view('filament.resources.api-mapping-resource.components.schema-analyzer')->render();
                                    })
                                    ->columnSpan('full'),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Test Verileri')
                            ->schema([
                                Forms\Components\Toggle::make('has_test_data')
                                    ->label('Test Verisi Ekle')
                                    ->reactive(),
                                    
                                Forms\Components\Textarea::make('test_data')
                                    ->label('Test Verisi')
                                    ->hidden(fn (callable $get) => !$get('has_test_data'))
                                    ->rows(15)
                                    ->columnSpan('full'),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Mapping Adı')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('apiUser.name')
                    ->label('API Kullanıcısı')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('source_type')
                    ->label('Kaynak Türü')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'UNKNOWN';
                        }
                        return strtoupper((string)$state);
                    }),
                    
                Tables\Columns\TextColumn::make('endpoint_path')
                    ->label('API Endpoint')
                    ->prefix(fn () => config('app.url'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('target_model')
                    ->label('Hedef Model')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'UNKNOWN';
                        }
                        return class_basename((string)$state);
                    })
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('target_operation')
                    ->label('İşlem'),
                    
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
                Tables\Filters\SelectFilter::make('source_type')
                    ->label('Kaynak Türü')
                    ->options([
                        'xml' => 'XML',
                        'json' => 'JSON',
                        'csv' => 'CSV',
                    ]),
                
                Tables\Filters\SelectFilter::make('api_user_id')
                    ->label('API Kullanıcısı')
                    ->relationship('apiUser', 'name'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->trueLabel('Sadece Aktif')
                    ->falseLabel('Sadece Pasif')
                    ->placeholder('Tümü'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('test')
                        ->label('Test Et')
                        ->icon('heroicon-m-beaker')
                        ->color('warning')
                        ->url(fn (ApiMapping $record) => route('api.mapping.test', $record))
                        ->openUrlInNewTab(),
                        
                    Tables\Actions\Action::make('view_endpoint')
                        ->label('Endpoint Bilgileri')
                        ->icon('heroicon-m-link')
                        ->color('info')
                        ->modalContent(fn (ApiMapping $record) => view('filament.resources.api-mapping-resource.components.endpoint-info', ['record' => $record])),
                        
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn (ApiMapping $record) => $record->is_active ? 'Pasif Yap' : 'Aktif Yap')
                        ->icon(fn (ApiMapping $record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                        ->color(fn (ApiMapping $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (ApiMapping $record) {
                            $record->is_active = !$record->is_active;
                            $record->save();
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktif Yap')
                        ->icon('heroicon-m-check-circle')
                        ->action(fn (Collection $records) => $records->each(fn (ApiMapping $record) => $record->update(['is_active' => true])))
                        ->color('success')
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Pasif Yap')
                        ->icon('heroicon-m-x-circle')
                        ->action(fn (Collection $records) => $records->each(fn (ApiMapping $record) => $record->update(['is_active' => false])))
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
            'index' => Pages\ListApiMappings::route('/'),
            'create' => Pages\CreateApiMapping::route('/create'),
            'edit' => Pages\EditApiMapping::route('/{record}/edit'),
        ];
    }
}