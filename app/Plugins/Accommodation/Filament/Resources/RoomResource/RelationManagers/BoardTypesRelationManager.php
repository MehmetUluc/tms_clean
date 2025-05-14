<?php

namespace App\Plugins\Accommodation\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoardTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'boardTypes';
    
    protected static ?string $title = 'Pansiyon Tipleri';
    
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pansiyon Tipi İlişkisi')
                    ->schema([
                        Forms\Components\Select::make('id')
                            ->label('Pansiyon Tipi')
                            ->relationship('boardTypes', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabledOn('edit'),
                            
                        Forms\Components\TextInput::make('price_modifier')
                            ->label('Fiyat Değiştiricisi')
                            ->required()
                            ->numeric()
                            ->minValue(-100)
                            ->maxValue(1000)
                            ->step(0.01)
                            ->default(0)
                            ->suffix('%')
                            ->helperText('Temel fiyata uygulanacak yüzde değişim. Örn: 10 için %10 daha pahalı, -10 için %10 daha ucuz.'),
                            
                        Forms\Components\Toggle::make('is_default')
                            ->label('Varsayılan Pansiyon Tipi')
                            ->helperText('Bu oda için varsayılan pansiyon tipi olarak ayarlar. Her oda için sadece bir varsayılan pansiyon tipi olabilir.')
                            ->default(false),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Pansiyon Tipi')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('pivot.price_modifier')
                    ->label('Fiyat Farkı')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? '%' . number_format($state, 2) : '%0.00'),
                    
                Tables\Columns\IconColumn::make('pivot.is_default')
                    ->label('Varsayılan')
                    ->boolean(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('price_modifier')
                            ->label('Fiyat Değiştiricisi (%)')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(-100)
                            ->maxValue(1000)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Temel fiyata uygulanacak yüzde değişim. Örn: 10 için %10 daha pahalı, -10 için %10 daha ucuz.'),
                            
                        Forms\Components\Toggle::make('is_default')
                            ->label('Varsayılan Pansiyon Tipi')
                            ->helperText('Bu oda için varsayılan pansiyon tipi olarak ayarlar. Her oda için sadece bir varsayılan pansiyon tipi olabilir.')
                            ->default(false),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Pansiyon Tipi İlişkisini Düzenle'),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}