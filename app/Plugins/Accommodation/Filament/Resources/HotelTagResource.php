<?php

namespace App\Plugins\Accommodation\Filament\Resources;

use App\Plugins\Accommodation\Filament\Resources\HotelTagResource\Pages;
use App\Plugins\Accommodation\Filament\Resources\HotelTagResource\RelationManagers;
use App\Plugins\Accommodation\Models\HotelTag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class HotelTagResource extends Resource
{
    protected static ?string $model = HotelTag::class;
    
    protected static ?string $modelLabel = 'Otel Etiketi';
    protected static ?string $pluralModelLabel = 'Otel Etiketleri';

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Otel Yönetimi';
    protected static ?int $navigationSort = 15;
    
    public static function canAccess(): bool
    {
        return true; // Geçici olarak herkesin erişimine izin ver
    }
    
    public static function canCreate(): bool
    {
        return true; // Geçici olarak herkesin oluşturmasına izin ver
    }
    
    public static function canEdit(Model $record): bool
    {
        return true; // Geçici olarak herkesin düzenlemesine izin ver
    }
    
    public static function canDelete(Model $record): bool
    {
        return true; // Geçici olarak herkesin silmesine izin ver
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Etiket Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tür')
                            ->options([
                                'category' => 'Kategori',
                                'amenity' => 'Özellik',
                                'feature' => 'Öne Çıkan Özellik',
                                'other' => 'Diğer',
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('icon')
                            ->label('İkon')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Öne Çıkan')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Etiket Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'category' => 'primary',
                        'amenity' => 'success', 
                        'feature' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('hotels_count')
                    ->label('Otel Sayısı')
                    ->counts('hotels')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\HotelsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotelTags::route('/'),
            'create' => Pages\CreateHotelTag::route('/create'),
            'edit' => Pages\EditHotelTag::route('/{record}/edit'),
        ];
    }
}