<?php

namespace App\Plugins\MenuManager\Filament\Resources;

use App\Plugins\MenuManager\Filament\Resources\MenuResource\Pages;
use App\Plugins\MenuManager\Filament\Resources\MenuResource\RelationManagers;
use App\Plugins\MenuManager\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    
    protected static ?string $navigationGroup = 'Content';
    
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Menu Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->helperText('Leave empty to auto-generate from name')
                            ->maxLength(255),
                        Forms\Components\Select::make('location')
                            ->options(config('menu-manager.locations'))
                            ->searchable(),
                        Forms\Components\Select::make('type')
                            ->options(config('menu-manager.types'))
                            ->default('default')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])->columnSpan(['lg' => 2]),
                
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('settings.cache_enabled')
                            ->label('Enable Caching')
                            ->default(true),
                        Forms\Components\TextInput::make('settings.cache_ttl')
                            ->label('Cache TTL (minutes)')
                            ->numeric()
                            ->default(1440),
                        Forms\Components\Toggle::make('settings.show_description')
                            ->label('Show Description')
                            ->default(false),
                        Forms\Components\ColorPicker::make('settings.bg_color')
                            ->label('Background Color'),
                        Forms\Components\Select::make('settings.container_class')
                            ->label('Container Class')
                            ->options([
                                'container' => 'Standard Container',
                                'container-fluid' => 'Full Width Container',
                                'container-sm' => 'Small Container',
                                'container-lg' => 'Large Container',
                            ])
                            ->default('container'),
                    ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->badge(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('location')
                    ->options(config('menu-manager.locations')),
                Tables\Filters\SelectFilter::make('type')
                    ->options(config('menu-manager.types')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('build')
                    ->label('Build Menu')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Menu $record): string => route('filament.admin.resources.menus.build', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Builder $query) => $query->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn (Builder $query) => $query->update(['is_active' => false])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MenuItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
            'build' => Pages\BuildMenu::route('/{record}/build'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}