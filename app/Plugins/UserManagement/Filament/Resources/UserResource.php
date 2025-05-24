<?php

namespace App\Plugins\UserManagement\Filament\Resources;

use App\Plugins\UserManagement\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use App\Plugins\Core\src\Traits\HasFilamentPermissions;

class UserResource extends Resource
{
    use HasFilamentPermissions;
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Sistem';
    protected static ?string $navigationLabel = 'Kullanıcılar';
    protected static ?int $navigationSort = 1;

    // Permission properties
    protected static ?string $viewAnyPermission = 'view_users';
    protected static ?string $viewPermission = 'view_users';
    protected static ?string $createPermission = 'create_users';
    protected static ?string $updatePermission = 'update_users';
    protected static ?string $deletePermission = 'delete_users';

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // First check permission from trait
        if (!parent::canEdit($record)) {
            return false;
        }
        
        // Additional logic: Users can edit their own accounts or admin can edit all accounts
        return auth()->id() === $record->id || auth()->id() === 1;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // First check permission from trait
        if (!parent::canDelete($record)) {
            return false;
        }
        
        // Additional logic: Admin user cannot be deleted and users cannot delete their own accounts
        // Only admins can delete other users
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
                    ->disabled(fn (\Illuminate\Database\Eloquent\Model $record = null) => $record && $record->id === 1) // Admin kullanıcısının rolü değiştirilemez
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