<?php

namespace App\Plugins\OTA\Filament\Resources\ChannelResource\RelationManagers;

use App\Plugins\OTA\Models\DataMapping;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MappingsRelationManager extends RelationManager
{
    protected static string $relationship = 'mappings';

    protected static ?string $title = 'Veri Eşleştirmeleri';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ad')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Select::make('format_type')
                    ->label('Format Tipi')
                    ->options([
                        'xml' => 'XML',
                        'json' => 'JSON'
                    ])
                    ->required(),

                Forms\Components\Select::make('operation_type')
                    ->label('İşlem Tipi')
                    ->options([
                        'import' => 'İçe Aktar (Dış Sistemden İçeri)',
                        'export' => 'Dışa Aktar (Bizden Dış Sisteme)',
                    ])
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
                    
                Forms\Components\Textarea::make('mapping_data')
                    ->label('Eşleştirme Verisi (JSON)')
                    ->helperText('Eşleştirme verisi JSON formatında olmalıdır')
                    ->required()
                    ->rows(10)
                    ->columnSpan('full'),

                Forms\Components\Textarea::make('template_content')
                    ->label('Şablon İçeriği')
                    ->helperText('Dönüşüm şablonu')
                    ->rows(10)
                    ->columnSpan('full'),

                Forms\Components\Textarea::make('sample_data')
                    ->label('Örnek Veri')
                    ->helperText('Test için örnek veri')
                    ->rows(5)
                    ->columnSpan('full'),
                    
                Forms\Components\KeyValue::make('validation_rules')
                    ->label('Doğrulama Kuralları')
                    ->keyLabel('Alan')
                    ->valueLabel('Kural')
                    ->addActionLabel('Kural Ekle')
                    ->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('format_type')
                    ->label('Format')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'xml' => 'danger',
                        'json' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('operation_type')
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
                Tables\Filters\SelectFilter::make('format_type')
                    ->label('Format')
                    ->options([
                        'xml' => 'XML',
                        'json' => 'JSON',
                    ]),

                Tables\Filters\SelectFilter::make('operation_type')
                    ->label('İşlem')
                    ->options([
                        'import' => 'İçe Aktar',
                        'export' => 'Dışa Aktar',
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
                    ->label('Aktif')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    
                    Tables\Actions\Action::make('test')
                        ->label('Test Et')
                        ->icon('heroicon-m-beaker')
                        ->color('warning')
                        ->action(function (DataMapping $record) {
                            // Test işlemi burada yapılacak
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
                        ->action(fn (DataMapping $records) => $records->each(fn (DataMapping $record) => $record->update(['is_active' => true])))
                        ->color('success')
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Pasif Yap')
                        ->icon('heroicon-m-x-circle')
                        ->action(fn (DataMapping $records) => $records->each(fn (DataMapping $record) => $record->update(['is_active' => false])))
                        ->color('danger')
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}