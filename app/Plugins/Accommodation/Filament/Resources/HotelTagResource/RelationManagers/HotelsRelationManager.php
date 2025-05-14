<?php

namespace App\Plugins\Accommodation\Filament\Resources\HotelTagResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelsRelationManager extends RelationManager
{
    protected static string $relationship = 'hotels';
    
    protected static ?string $title = 'Oteller';

    public static function canAccess(): bool
    {
        return true; // Geçici olarak herkese izin ver
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Otel Adı')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Otel Tipi')
                    ->options([
                        'hotel' => 'Otel',
                        'resort' => 'Resort',
                        'boutique' => 'Butik Otel',
                        'villa' => 'Villa',
                        'apartment' => 'Apart',
                        'hostel' => 'Hostel',
                    ])
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
                Tables\Columns\TextColumn::make('region.name')
                    ->label('Bölge'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->color(function ($state) {
                        if ($state === null) {
                            return 'gray';
                        }
                        
                        return match ($state) {
                            'hotel' => 'primary',
                            'resort' => 'success', 
                            'boutique' => 'warning',
                            'villa' => 'danger',
                            'apartment' => 'info',
                            default => 'gray',
                        };
                    }),
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
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}