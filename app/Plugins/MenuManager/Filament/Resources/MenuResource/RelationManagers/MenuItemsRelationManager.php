<?php

namespace App\Plugins\MenuManager\Filament\Resources\MenuResource\RelationManagers;

use App\Plugins\MenuManager\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Route;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'allItems';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Item Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('link_type')
                            ->options(config('menu-manager.link_types'))
                            ->default('url')
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'url')
                            ->maxLength(255),
                        Forms\Components\Select::make('route_name')
                            ->options($this->getRoutes())
                            ->searchable()
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'route'),
                        Forms\Components\Select::make('model_type')
                            ->options(config('menu-manager.linkable_models'))
                            ->searchable()
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'model')
                            ->live(),
                        Forms\Components\Select::make('model_id')
                            ->options(function (Forms\Get $get) {
                                $modelType = $get('model_type');
                                if (!$modelType) return [];
                                
                                if (class_exists($modelType)) {
                                    return $modelType::all()->pluck('name', 'id');
                                }
                                
                                return [];
                            })
                            ->searchable()
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'model' && $get('model_type')),
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Item')
                            ->options(function (RelationManager $livewire) {
                                return $livewire->ownerRecord->allItems()
                                    ->pluck('title', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->placeholder('No Parent (Root Level)'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Display Options')
                    ->schema([
                        Forms\Components\Select::make('target')
                            ->options(config('menu-manager.targets'))
                            ->default('_self')
                            ->required(),
                        Forms\Components\TextInput::make('icon')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('class')
                            ->label('CSS Class')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false),
                        Forms\Components\Toggle::make('is_mega_menu')
                            ->label('Mega Menu')
                            ->default(false)
                            ->live(),
                        
                        Forms\Components\Section::make('Mega Menu Settings')
                            ->schema([
                                Forms\Components\Select::make('mega_menu_template')
                                    ->label('Mega Menu Template')
                                    ->options([
                                        'simple-columns' => 'Simple Columns',
                                        'featured-image' => 'Featured Image with Links',
                                        'categorized' => 'Categorized Links',
                                        'mixed-content' => 'Mixed Content',
                                        'custom' => 'Custom Layout',
                                    ]),
                                    
                                Forms\Components\Select::make('mega_menu_columns')
                                    ->label('Number of Columns')
                                    ->options([
                                        2 => '2 Columns',
                                        3 => '3 Columns',
                                        4 => '4 Columns',
                                        5 => '5 Columns',
                                        6 => '6 Columns',
                                    ])
                                    ->default(4),
                                    
                                Forms\Components\Select::make('mega_menu_width')
                                    ->label('Menu Width')
                                    ->options([
                                        'container' => 'Container Width',
                                        'full' => 'Full Width',
                                        'custom' => 'Custom Width',
                                    ])
                                    ->default('container'),
                                    
                                Forms\Components\ColorPicker::make('mega_menu_background')
                                    ->label('Background Color'),
                                    
                                Forms\Components\FileUpload::make('mega_menu_bg_image')
                                    ->label('Background Image')
                                    ->disk('public')
                                    ->directory('menu-backgrounds')
                                    ->visibility('public')
                                    ->image(),
                            ])
                            ->visible(fn (Forms\Get $get): bool => $get('is_mega_menu') === true)
                            ->collapsed(),
                            
                        Forms\Components\Section::make('Mega Menu Content Builder')
                            ->schema([
                                Forms\Components\Repeater::make('mega_menu_content')
                                    ->label('Columns')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Column Title')
                                            ->required(),
                                            
                                        Forms\Components\Select::make('width')
                                            ->label('Column Width')
                                            ->options([
                                                'equal' => 'Equal Width',
                                                'narrow' => 'Narrow (25%)',
                                                'medium' => 'Medium (33%)',
                                                'wide' => 'Wide (50%)',
                                                'extra-wide' => 'Extra Wide (66%)',
                                                'full' => 'Full Width (100%)',
                                            ])
                                            ->default('equal'),
                                            
                                        Forms\Components\Select::make('content_type')
                                            ->label('Content Type')
                                            ->options([
                                                'links' => 'Simple Links',
                                                'featured' => 'Featured Content',
                                                'html' => 'Custom HTML',
                                                'image' => 'Image',
                                            ])
                                            ->default('links')
                                            ->live(),
                                            
                                        Forms\Components\Repeater::make('links')
                                            ->label('Links')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Link Title')
                                                    ->required(),
                                                Forms\Components\TextInput::make('url')
                                                    ->label('URL')
                                                    ->required(),
                                                Forms\Components\Toggle::make('featured')
                                                    ->label('Highlight this link')
                                                    ->default(false),
                                                Forms\Components\TextInput::make('icon')
                                                    ->label('Icon (CSS class)'),
                                            ])
                                            ->visible(fn (Forms\Get $get): bool => $get('content_type') === 'links')
                                            ->columns(2),
                                            
                                        Forms\Components\FileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->disk('public')
                                            ->directory('menu-featured')
                                            ->visible(fn (Forms\Get $get): bool => $get('content_type') === 'featured' || $get('content_type') === 'image')
                                            ->image(),
                                            
                                        Forms\Components\TextInput::make('featured_title')
                                            ->label('Featured Title')
                                            ->visible(fn (Forms\Get $get): bool => $get('content_type') === 'featured'),
                                            
                                        Forms\Components\Textarea::make('featured_description')
                                            ->label('Featured Description')
                                            ->rows(2)
                                            ->visible(fn (Forms\Get $get): bool => $get('content_type') === 'featured'),
                                            
                                        Forms\Components\TextInput::make('featured_url')
                                            ->label('Featured URL')
                                            ->visible(fn (Forms\Get $get): bool => $get('content_type') === 'featured' || $get('content_type') === 'image'),
                                            
                                        Forms\Components\RichEditor::make('html_content')
                                            ->label('HTML Content')
                                            ->visible(fn (Forms\Get $get): bool => $get('content_type') === 'html')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'redo',
                                                'strike',
                                                'underline',
                                                'undo',
                                            ]),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                    ->maxItems(function (Forms\Get $get) {
                                        return $get('../../mega_menu_columns') ?? 4;
                                    })
                                    ->defaultItems(function (Forms\Get $get) {
                                        $columns = $get('../../mega_menu_columns') ?? 4;
                                        return max(1, min(4, $columns));
                                    })
                                    ->collapsible()
                                    ->collapseAllAction(
                                        fn (Forms\Components\Actions\Action $action) => $action->label('Collapse All')
                                    )
                                    ->reorderableWithButtons(),
                            ])
                            ->visible(fn (Forms\Get $get): bool => $get('is_mega_menu') === true)
                            ->collapsed(),
                        Forms\Components\Select::make('template')
                            ->options(config('menu-manager.templates'))
                            ->placeholder('No Template'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Advanced Options')
                    ->schema([
                        Forms\Components\KeyValue::make('attributes')
                            ->label('HTML Attributes')
                            ->keyLabel('Attribute')
                            ->valueLabel('Value')
                            ->addActionLabel('Add Attribute'),
                        Forms\Components\KeyValue::make('data')
                            ->label('Custom Data')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->addActionLabel('Add Data'),
                    ])->collapsed(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('order')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('url')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->limit(30),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Item')
                    ->options(function (RelationManager $livewire) {
                        return $livewire->ownerRecord->allItems()
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->placeholder('Root Level Items')
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value'])) {
                            return $query->where('parent_id', $data['value']);
                        }
                        
                        return $query->whereNull('parent_id');
                    }),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Status'),
                Tables\Filters\TernaryFilter::make('is_mega_menu')
                    ->label('Mega Menu'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (MenuItem $record): void {
                        // Move children up a level before deleting
                        foreach ($record->children as $child) {
                            $child->update([
                                'parent_id' => $record->parent_id
                            ]);
                        }
                        
                        $record->delete();
                    }),
                Tables\Actions\Action::make('move_up')
                    ->label('Move Up')
                    ->icon('heroicon-o-arrow-up')
                    ->action(function (MenuItem $record): void {
                        $this->moveItemUp($record);
                    })
                    ->hidden(fn (MenuItem $record): bool => $record->order <= 1),
                Tables\Actions\Action::make('move_down')
                    ->label('Move Down')
                    ->icon('heroicon-o-arrow-down')
                    ->action(function (MenuItem $record): void {
                        $this->moveItemDown($record);
                    }),
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
            ])
            ->reorderable('order')
            ->defaultSort('order');
    }
    
    protected function getRoutes(): array
    {
        $routes = Route::getRoutes();
        $routeNames = [];
        
        foreach ($routes as $route) {
            if ($route->getName()) {
                $routeNames[$route->getName()] = $route->getName() . ' (' . $route->uri() . ')';
            }
        }
        
        return $routeNames;
    }
    
    protected function moveItemUp(MenuItem $item): void
    {
        $previousItem = MenuItem::where('menu_id', $item->menu_id)
            ->where('parent_id', $item->parent_id)
            ->where('order', '<', $item->order)
            ->orderBy('order', 'desc')
            ->first();
            
        if ($previousItem) {
            $previousOrder = $previousItem->order;
            $previousItem->update(['order' => $item->order]);
            $item->update(['order' => $previousOrder]);
        }
    }
    
    protected function moveItemDown(MenuItem $item): void
    {
        $nextItem = MenuItem::where('menu_id', $item->menu_id)
            ->where('parent_id', $item->parent_id)
            ->where('order', '>', $item->order)
            ->orderBy('order', 'asc')
            ->first();
            
        if ($nextItem) {
            $nextOrder = $nextItem->order;
            $nextItem->update(['order' => $item->order]);
            $item->update(['order' => $nextOrder]);
        }
    }
}