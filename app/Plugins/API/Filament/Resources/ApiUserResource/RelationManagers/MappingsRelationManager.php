<?php

namespace App\Plugins\API\Filament\Resources\ApiUserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\RoomInventory;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Pricing\Models\RatePlan;

class MappingsRelationManager extends RelationManager
{
    protected static string $relationship = 'mappings';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Mapping Adı')
                            ->required()
                            ->maxLength(255),
                            
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
                    ]),
                    
                Forms\Components\Section::make('Kaynak ve Hedef Ayarları')
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
                            
                        Forms\Components\Select::make('target_model')
                            ->label('Hedef Model')
                            ->options([
                                Room::class => 'Oda',
                                RoomInventory::class => 'Oda Envanteri',
                                RatePlan::class => 'Fiyat Planı',
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
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Alan Eşleştirme')
                    ->schema([
                        Forms\Components\KeyValue::make('field_mappings')
                            ->label('Alan Eşleştirmeleri')
                            ->keyLabel('Kaynak Alan')
                            ->valueLabel('Hedef Alan')
                            ->keyPlaceholder('Örn: Room.Id veya id')
                            ->valuePlaceholder('Örn: external_id veya price')
                            ->required(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Mapping Adı')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('source_type')
                    ->label('Kaynak Türü')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'UNKNOWN';
                        }
                        return strtoupper((string)$state);
                    })
                    ->colors([
                        'primary' => 'xml',
                        'success' => 'json',
                        'warning' => 'csv',
                    ]),
                    
                Tables\Columns\TextColumn::make('endpoint_path')
                    ->label('API Endpoint')
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
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('last_sync_at')
                    ->label('Son Senkronizasyon')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source_type')
                    ->label('Kaynak Türü')
                    ->options([
                        'xml' => 'XML',
                        'json' => 'JSON',
                        'csv' => 'CSV',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->trueLabel('Sadece Aktif')
                    ->falseLabel('Sadece Pasif')
                    ->placeholder('Tümü'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, RelationManager $livewire): mixed {
                        return $livewire->getOwnerRecord()->mappings()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn ($record) => $record->is_active ? 'Pasif Yap' : 'Aktif Yap')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->is_active = !$record->is_active;
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}