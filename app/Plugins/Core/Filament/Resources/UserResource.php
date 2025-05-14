<?php

namespace App\Plugins\Core\Filament\Resources;

use App\Plugins\Core\Filament\Resources\UserResource\Pages;
use App\Plugins\Core\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Sistem';
    protected static ?string $navigationLabel = 'Kullanıcılar';
    protected static ?int $navigationSort = 1;

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
        // Kullanıcılar kendi hesaplarını düzenleyebilir veya admin her hesabı düzenleyebilir
        return auth()->id() === $record->id || auth()->id() === 1;
    }

    public static function canDelete(Model $record): bool
    {
        // Admin kullanıcı silinemez ve kullanıcılar kendi hesaplarını silemez
        // Sadece adminler başka kullanıcıları silebilir
        return $record->id !== 1 && auth()->id() !== $record->id && auth()->id() === 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                
                Forms\Components\TextInput::make('password')
                    ->label('Şifre')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                
                Forms\Components\Select::make('roles')
                    ->label('Roller')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->disabled(fn (Model $record = null) => $record && $record->id === 1) // Admin kullanıcısının rolü değiştirilemez
                    ->visible(auth()->id() === 1), // Sadece admin rolleri görebilir ve değiştirebilir
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roller')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record) => static::canEdit($record)),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => static::canDelete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(auth()->id() === 1) // Sadece admin bulk silme yapabilir
                        ->before(function (Tables\Actions\DeleteBulkAction $action) {
                            // Admin kullanıcısını seçim listesinden kaldır
                            $action->records = $action->records->reject(fn (User $user) => $user->id === 1);
                            
                            if ($action->records->isEmpty()) {
                                $action->cancel();
                            }
                        }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}