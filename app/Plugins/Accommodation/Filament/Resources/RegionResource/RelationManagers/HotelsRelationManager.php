<?php

namespace App\Plugins\Accommodation\Filament\Resources\RegionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Plugins\Accommodation\Models\Region;

class HotelsRelationManager extends RelationManager
{
    protected static string $relationship = 'hotels';
    
    protected static ?string $title = 'Oteller';

    public static function canAccess(): bool
    {
        return true; // Geçici olarak herkese izin ver
    }
    
    /**
     * Get the URL for viewing the relation manager.
     *
     * @param \App\Plugins\Accommodation\Models\Region $record
     * @return string
     */
    public static function getUrl($record): string
    {
        return \App\Plugins\Accommodation\Filament\Resources\RegionResource::getUrl('edit', ['record' => $record]) . '#relation-' . static::class;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Otel Adı')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type_id')
                    ->label('Otel Tipi')
                    ->relationship('type', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('stars')
                    ->label('Yıldız')
                    ->options([
                        1 => '1 Yıldız',
                        2 => '2 Yıldız',
                        3 => '3 Yıldız',
                        4 => '4 Yıldız',
                        5 => '5 Yıldız',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('city')
                    ->label('Şehir'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Otel Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tip')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('stars')
                    ->label('Yıldız')
                    ->formatStateUsing(fn ($state): string => $state ? str_repeat('⭐', $state) : ''),
                Tables\Columns\TextColumn::make('city')
                    ->label('Şehir'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}