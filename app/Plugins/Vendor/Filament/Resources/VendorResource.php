<?php

namespace App\Plugins\Vendor\Filament\Resources;

use App\Plugins\Vendor\Filament\Resources\VendorResource\Pages;
use App\Plugins\Vendor\Models\Vendor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Vendor Management';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('Vendors');
    }

    public static function getPluralLabel(): string
    {
        return __('Vendors');
    }

    public static function getLabel(): string
    {
        return __('Vendor');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Vendor')
                    ->tabs([
                        Tabs\Tab::make('General Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Vendor Details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('user_id')
                                                    ->label('Associated User')
                                                    ->relationship('user', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->createOptionForm([
                                                        Grid::make(2)
                                                            ->schema([
                                                                TextInput::make('name')
                                                                    ->required(),
                                                                TextInput::make('email')
                                                                    ->email()
                                                                    ->required(),
                                                                TextInput::make('password')
                                                                    ->password()
                                                                    ->required()
                                                                    ->confirmed(),
                                                                TextInput::make('password_confirmation')
                                                                    ->password()
                                                                    ->required(),
                                                            ]),
                                                    ]),

                                                TextInput::make('company_name')
                                                    ->required()
                                                    ->maxLength(255),
                                                
                                                TextInput::make('tax_number')
                                                    ->maxLength(50),
                                                
                                                TextInput::make('tax_office')
                                                    ->maxLength(100),
                                                
                                                TextInput::make('phone')
                                                    ->tel()
                                                    ->maxLength(20),
                                                
                                                TextInput::make('website')
                                                    ->url()
                                                    ->maxLength(255),
                                            ]),
                                    ]),
                                
                                Section::make('Address Information')
                                    ->schema([
                                        Textarea::make('address')
                                            ->rows(3)
                                            ->maxLength(500),
                                        
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('city')
                                                    ->maxLength(100),
                                                
                                                TextInput::make('postal_code')
                                                    ->maxLength(20),
                                                
                                                TextInput::make('country')
                                                    ->maxLength(100)
                                                    ->default('Turkey'),
                                            ]),
                                    ]),
                                
                                Section::make('Contact Person')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('contact_person')
                                                    ->maxLength(255),
                                                
                                                TextInput::make('contact_email')
                                                    ->email()
                                                    ->maxLength(255),
                                                
                                                TextInput::make('contact_phone')
                                                    ->tel()
                                                    ->maxLength(20),
                                            ]),
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Contract & Commission')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Contract Details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('status')
                                                    ->options([
                                                        'pending' => 'Pending',
                                                        'active' => 'Active',
                                                        'inactive' => 'Inactive',
                                                        'suspended' => 'Suspended',
                                                    ])
                                                    ->required()
                                                    ->default('pending'),
                                                
                                                Grid::make(2)
                                                    ->schema([
                                                        DatePicker::make('contract_start_date')
                                                            ->default(now()),
                                                        
                                                        DatePicker::make('contract_end_date'),
                                                    ]),
                                            ]),
                                    ]),
                                
                                Section::make('Commission Settings')
                                    ->schema([
                                        Grid::make(1)
                                            ->schema([
                                                TextInput::make('default_commission_rate')
                                                    ->label('Default Commission Rate (%)')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->default(10)
                                                    ->suffix('%')
                                                    ->required(),
                                            ]),
                                    ]),
                                
                                Section::make('Notes')
                                    ->schema([
                                        Textarea::make('notes')
                                            ->rows(4)
                                            ->maxLength(1000),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                
                TextColumn::make('user.name')
                    ->label('Contact Person')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('city')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('default_commission_rate')
                    ->label('Commission (%)')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => number_format($state, 2) . '%'),
                
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ])
                    ->sortable(),
                
                TextColumn::make('hotels_count')
                    ->label('Hotels')
                    ->counts('hotels')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Action::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Vendor $record) => $record->status !== 'active')
                        ->requiresConfirmation()
                        ->action(fn (Vendor $record) => $record->update(['status' => 'active'])),
                    
                    Action::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Vendor $record) => $record->status === 'active')
                        ->requiresConfirmation()
                        ->action(fn (Vendor $record) => $record->update(['status' => 'inactive'])),
                    
                    Action::make('manageCommission')
                        ->label('Manage Commission')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->url(fn (Vendor $record) => static::getUrl('manage-commission', ['record' => $record])),
                    
                    Action::make('viewHotels')
                        ->label('View Hotels')
                        ->icon('heroicon-o-building-office-2')
                        ->url(fn (Vendor $record) => static::getUrl('view-hotels', ['record' => $record])),
                    
                    Action::make('viewFinancials')
                        ->label('View Financials')
                        ->icon('heroicon-o-banknotes')
                        ->url(fn (Vendor $record) => static::getUrl('view-financials', ['record' => $record])),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    BulkAction::make('activateBulk')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'active'])),
                    
                    BulkAction::make('deactivateBulk')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'inactive'])),
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
            'view' => Pages\ViewVendor::route('/{record}'),
            'manage-commission' => Pages\ManageVendorCommission::route('/{record}/commission'),
            'view-hotels' => Pages\ViewVendorHotels::route('/{record}/hotels'),
            'view-financials' => Pages\ViewVendorFinancials::route('/{record}/financials'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}