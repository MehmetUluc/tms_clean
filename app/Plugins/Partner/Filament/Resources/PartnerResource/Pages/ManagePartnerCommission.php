<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerResource;
use App\Plugins\Partner\Models\PartnerCommission;
use App\Plugins\Partner\Services\PartnerService;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ManagePartnerCommission extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = PartnerResource::class;

    protected static ?string $title = 'Manage Commission';

    protected static string $view = 'filament.pages.manage-partner-commission';

    public $partner;
    public $commissionData = [];

    public function mount($record): void
    {
        $this->partner = $record;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Add Commission Rate')
                    ->description('Add a new commission rate for this partner')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('hotel_id')
                                    ->label('Hotel')
                                    ->relationship('hotels', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                
                                Forms\Components\Select::make('room_type_id')
                                    ->label('Room Type')
                                    ->options(function (Forms\Get $get) {
                                        $hotelId = $get('hotel_id');
                                        
                                        if (!$hotelId) {
                                            return [];
                                        }
                                        
                                        return \App\Plugins\Accommodation\Models\RoomType::whereHas('rooms', function ($query) use ($hotelId) {
                                            $query->where('hotel_id', $hotelId);
                                        })->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                
                                Forms\Components\Select::make('board_type_id')
                                    ->label('Board Type')
                                    ->relationship('boardTypes', 'name')
                                    ->searchable()
                                    ->preload(),
                                
                                Forms\Components\TextInput::make('commission_rate')
                                    ->label('Commission Rate (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(10)
                                    ->required()
                                    ->suffix('%'),
                                
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Start Date'),
                                
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->after('start_date'),
                            ]),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                            
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('addCommission')
                                ->label('Add Commission Rate')
                                ->submit()
                                ->icon('heroicon-o-plus')
                                ->color('primary')
                                ->action(function (array $data) {
                                    $this->addCommissionRate($data);
                                }),
                        ]),
                    ]),
            ]);
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return PartnerCommission::query()
            ->where('partner_id', $this->partner->id);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PartnerCommission::query()
                    ->where('partner_id', $this->partner->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Hotel')
                    ->searchable()
                    ->sortable()
                    ->default('All Hotels'),
                
                Tables\Columns\TextColumn::make('roomType.name')
                    ->label('Room Type')
                    ->searchable()
                    ->sortable()
                    ->default('All Room Types'),
                
                Tables\Columns\TextColumn::make('boardType.name')
                    ->label('Board Type')
                    ->searchable()
                    ->sortable()
                    ->default('All Board Types'),
                
                Tables\Columns\TextColumn::make('commission_rate')
                    ->label('Commission Rate')
                    ->formatStateUsing(fn (string $state): string => number_format($state, 2) . '%')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Commission Rate')
                    ->modalSubmitActionLabel('Update')
                    ->form([
                        Forms\Components\TextInput::make('commission_rate')
                            ->label('Commission Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->suffix('%'),
                        
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->after('start_date'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500),
                    ]),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function addCommissionRate(array $data): void
    {
        DB::beginTransaction();
        
        try {
            $partnerService = app(PartnerService::class);

            $commission = $partnerService->setCommissionRate(
                $this->partner,
                $data['commission_rate'],
                $data['hotel_id'] ?? null,
                $data['room_type_id'] ?? null,
                $data['board_type_id'] ?? null,
                $data['start_date'] ?? null,
                $data['end_date'] ?? null,
                $data['description'] ?? null,
                auth()->id()
            );
            
            // If this is the default commission rate, update the partner record
            if (!$data['hotel_id'] && !$data['room_type_id'] && !$data['board_type_id']) {
                $this->partner->update(['default_commission_rate' => $data['commission_rate']]);
            }
            
            DB::commit();
            
            // Reset the form
            $this->form->fill();
            
            // Refresh the page
            $this->redirect(static::getUrl(['record' => $this->partner]));
            
            Notification::make()
                ->title('Commission rate added successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Failed to add commission rate')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToPartner')
                ->label('Back to Partner')
                ->url(fn () => PartnerResource::getUrl('view', ['record' => $this->partner])),
        ];
    }
}