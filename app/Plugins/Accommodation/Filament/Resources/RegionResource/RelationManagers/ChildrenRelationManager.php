<?php

namespace App\Plugins\Accommodation\Filament\Resources\RegionResource\RelationManagers;

use App\Plugins\Accommodation\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';
    
    protected static ?string $title = 'Alt Bölgeler';
    protected static ?string $label = 'Alt Bölge';
    protected static ?string $pluralLabel = 'Alt Bölgeler';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Bölge Tipi')
                    ->options(Region::getTypeLabels())
                    ->default(function(RelationManager $livewire) {
                        // Üst bölgeye göre varsayılan değer ayarla
                        $parent = $livewire->getOwnerRecord();
                        
                        if ($parent->type === Region::TYPE_COUNTRY) {
                            return Region::TYPE_REGION;
                        } elseif ($parent->type === Region::TYPE_REGION) {
                            return Region::TYPE_CITY;
                        } elseif ($parent->type === Region::TYPE_CITY) {
                            return Region::TYPE_DISTRICT;
                        }
                        
                        return Region::TYPE_REGION;
                    })
                    ->required(),
                    
                Forms\Components\TextInput::make('name')
                    ->label('Bölge Adı')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('code')
                    ->label('Bölge Kodu')
                    ->maxLength(10),
                    
                Forms\Components\Textarea::make('description')
                    ->label('Açıklama'),
                    
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Enlem')
                            ->numeric()
                            ->step(0.0000001),
                            
                        Forms\Components\TextInput::make('longitude')
                            ->label('Boylam')
                            ->numeric()
                            ->step(0.0000001),
                    ])
                    ->columns(2),
                    
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                            
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Öne Çıkan')
                            ->default(false),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Alt Bölge Adı')
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
                    
                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('children_count')
                    ->label('Alt Bölge Sayısı')
                    ->counts('children')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('hotels_count')
                    ->label('Otel Sayısı')
                    ->counts('hotels')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean(),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Alt Bölge Ekle')
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        // Alt bölge eklerken parent_id'yi otomatik ayarla
                        $data['parent_id'] = $livewire->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}