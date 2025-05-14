<?php

namespace App\Plugins\Accommodation\Filament\Resources;

use App\Plugins\Accommodation\Filament\Resources\RegionResource\Pages;
use App\Plugins\Accommodation\Filament\Resources\RegionResource\RelationManagers;
use App\Plugins\Accommodation\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $modelLabel = 'Bölge';
    protected static ?string $pluralModelLabel = 'Bölgeler';

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Otel Yönetimi';
    protected static ?int $navigationSort = 20;

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
                Forms\Components\Tabs::make('Bölge Bilgileri')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Temel Bilgiler')
                            ->schema([
                                Forms\Components\Select::make('parent_id')
                                    ->label('Üst Bölge')
                                    ->relationship('parent', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1)
                                    ->helperText('Bu bölgenin bağlı olduğu üst bölge'),

                                Forms\Components\Select::make('type')
                                    ->label('Bölge Tipi')
                                    ->options(Region::getTypeLabels())
                                    ->default(Region::TYPE_REGION)
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('name')
                                    ->label('Bölge Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, callable $set) {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Web adresinde görünecek kısa ad (otomatik oluşturulur)')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('code')
                                    ->label('Bölge Kodu')
                                    ->maxLength(10)
                                    ->columnSpan(1)
                                    ->visible(fn (callable $get) => in_array($get('type'), [Region::TYPE_COUNTRY, Region::TYPE_REGION])),

                                Forms\Components\Textarea::make('description')
                                    ->label('Açıklama')
                                    ->columnSpan(2),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Konum Bilgileri')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('latitude')
                                            ->label('Enlem')
                                            ->numeric()
                                            ->step(0.0000001)
                                            ->placeholder('Örn: 36.8969')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('longitude')
                                            ->label('Boylam')
                                            ->numeric()
                                            ->step(0.0000001)
                                            ->placeholder('Örn: 30.7133')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('timezone')
                                            ->label('Saat Dilimi')
                                            ->placeholder('Örn: Europe/Istanbul')
                                            ->columnSpan(2),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Görünüm Ayarları')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->helperText('Bu bölge aktif olarak görüntülenecek')
                                            ->default(true)
                                            ->columnSpan(1),

                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Öne Çıkan')
                                            ->helperText('Bu bölge öne çıkan bölgeler arasında gösterilecek')
                                            ->default(false)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Sıralama')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Bölgelerin görüntülenme sırası (küçük değerler önce gösterilir)')
                                            ->columnSpan(2),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Bölge Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->formatStateUsing(function ($state, Region $record) {
                        try {
                            return $record->getTypeLabel();
                        } catch (\Throwable $e) {
                            return 'Belirtilmemiş';
                        }
                    })
                    ->badge()
                    ->color(function ($state) {
                        if ($state === null) {
                            return 'gray';
                        }

                        return match ($state) {
                            Region::TYPE_COUNTRY => 'success',
                            Region::TYPE_REGION => 'info',
                            Region::TYPE_CITY => 'warning',
                            Region::TYPE_DISTRICT => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_path')
                    ->label('Tam Yol')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Her seviyede arama yapmak için
                        return $query->whereHas('parent', function (Builder $query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhere('name', 'like', "%{$search}%");
                    })
                    ->tooltip(function (Region $record) {
                        try {
                            return $record->full_path ?? 'Belirtilmemiş';
                        } catch (\Throwable $e) {
                            return 'Belirtilmemiş';
                        }
                    })
                    ->limit(30),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('children_count')
                    ->label('Alt Bölge Sayısı')
                    ->counts('children')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotels_count')
                    ->label('Otel Sayısı')
                    ->counts('hotels')
                    ->sortable(),
                Tables\Columns\TextColumn::make('all_hotels_count')
                    ->label('Toplam Otel Sayısı')
                    ->tooltip('Bu bölge ve alt bölgelerdeki tüm otellerin sayısı')
                    ->state(function (Region $record): int {
                        // Bu bölge ve alt bölgelerdeki tüm otelleri say
                        $childRegionIds = $record->getAllChildrenIdsAttribute();
                        $allRegionIds = array_merge([$record->id], $childRegionIds);
                        return \App\Plugins\Accommodation\Models\Hotel::whereIn('region_id', $allRegionIds)->count();
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Bu özel sütun için sıralama mantığı uygulanamaz,
                        // çünkü hesaplanan bir değer.
                        return $query->orderBy('id', $direction);
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable()
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('type')
                    ->label('Bölge Tipi')
                    ->options(Region::getTypeLabels())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Üst Bölge')
                    ->relationship('parent', 'name')
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Öne Çıkan'),
                Tables\Filters\TernaryFilter::make('has_hotels')
                    ->label('Oteli Var')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereHas('hotels'),
                        false: fn (Builder $query): Builder => $query->whereDoesntHave('hotels'),
                    ),
                Tables\Filters\TernaryFilter::make('has_children')
                    ->label('Alt Bölgesi Var')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereHas('children'),
                        false: fn (Builder $query): Builder => $query->whereDoesntHave('children'),
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_hotels')
                        ->label('Otelleri Gör')
                        ->color('info')
                        ->icon('heroicon-s-building-office-2')
                        ->url(fn (Region $record): string => RegionResource\RelationManagers\HotelsRelationManager::getUrl($record)),

                    Tables\Actions\Action::make('add_subregion')
                        ->label('Alt Bölge Ekle')
                        ->color('success')
                        ->icon('heroicon-s-plus')
                        ->url(fn (Region $record): string => RegionResource::getUrl('create', [
                            'parent_id' => $record->id
                        ])),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifleştir')
                        ->color('success')
                        ->icon('heroicon-s-check')
                        ->action(function (Collection $records): void {
                            $records->each(function (Region $record): void {
                                $record->update(['is_active' => true]);
                            });
                        }),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Devre Dışı Bırak')
                        ->color('danger')
                        ->icon('heroicon-s-x-mark')
                        ->action(function (Collection $records): void {
                            $records->each(function (Region $record): void {
                                $record->update(['is_active' => false]);
                            });
                        }),

                    Tables\Actions\BulkAction::make('featured')
                        ->label('Öne Çıkar')
                        ->color('info')
                        ->icon('heroicon-s-star')
                        ->action(function (Collection $records): void {
                            $records->each(function (Region $record): void {
                                $record->update(['is_featured' => true]);
                            });
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            // Alt bölgeler ilişkisi
            RelationManagers\ChildrenRelationManager::class,

            // Bölgeye ait oteller ilişkisi
            RelationManagers\HotelsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}