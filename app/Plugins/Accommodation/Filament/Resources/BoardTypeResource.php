<?php

namespace App\Plugins\Accommodation\Filament\Resources;

use App\Plugins\Accommodation\Filament\Resources\BoardTypeResource\Pages;
use App\Plugins\Booking\Models\BoardType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoardTypeResource extends Resource
{
    protected static ?string $model = BoardType::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';
    
    protected static ?string $navigationGroup = 'Otel Yönetimi';
    
    protected static ?string $navigationLabel = 'Pansiyon Tipleri';
    
    protected static ?int $navigationSort = 35;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Pansiyon Tipi Adı')
                                    ->required()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('code')
                                    ->label('Kısa Kod')
                                    ->required()
                                    ->maxLength(10)
                                    ->helperText('Örneğin: AI (All Inclusive), HB (Half Board)')
                                    ->unique(ignoreRecord: true),
                            ]),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpan('full'),
                            
                        Forms\Components\TextInput::make('icon')
                            ->label('İkon')
                            ->maxLength(255)
                            ->helperText('İkon adı veya sınıfı (Font Awesome vb.)')
                            ->placeholder('fa-utensils'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columnSpan(['lg' => 2]),
                    
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->content(fn (?BoardType $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                            
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Güncellenme Tarihi')
                            ->content(fn (?BoardType $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columnSpan(['lg' => 1]),
                    
                Forms\Components\Section::make('Dahil Olan ve Olmayan Öğeler')
                    ->schema([
                        Forms\Components\Repeater::make('includes')
                            ->label('Dahil Olan Öğeler')
                            ->schema([
                                Forms\Components\TextInput::make('item')
                                    ->label('Öğe')
                                    ->required()
                                    ->placeholder('Örn: Kahvaltı'),
                                Forms\Components\TextInput::make('description')
                                    ->label('Açıklama')
                                    ->placeholder('Örn: Açık büfe kahvaltı'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->columnSpan('full'),
                            
                        Forms\Components\Repeater::make('excludes')
                            ->label('Hariç Tutulan Öğeler')
                            ->schema([
                                Forms\Components\TextInput::make('item')
                                    ->label('Öğe')
                                    ->required()
                                    ->placeholder('Örn: İçecekler'),
                                Forms\Components\TextInput::make('description')
                                    ->label('Açıklama')
                                    ->placeholder('Örn: Alkollü içecekler hariç'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->columnSpan('full'),
                    ])
                    ->columnSpan('full'),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Ek Notlar')
                    ->maxLength(1000)
                    ->columnSpan('full'),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Pansiyon Tipi')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable(),
                
                // Rooms ilişkisi kaldırıldı - artık HotelBoardType üzerinden yönetiliyor
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıralama')
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
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBoardTypes::route('/'),
            'create' => Pages\CreateBoardType::route('/create'),
            'edit' => Pages\EditBoardType::route('/{record}/edit'),
        ];
    }    
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}