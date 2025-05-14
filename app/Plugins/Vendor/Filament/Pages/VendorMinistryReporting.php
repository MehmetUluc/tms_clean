<?php

namespace App\Plugins\Vendor\Filament\Pages;

use App\Plugins\Vendor\Models\VendorMinistryReport;
use App\Plugins\Vendor\Services\VendorService;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class VendorMinistryReporting extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string $view = 'filament.pages.vendor-ministry-reporting';
    
    protected static ?string $title = 'Ministry Reporting';
    
    protected static ?string $navigationGroup = 'Vendor';
    
    protected static ?int $navigationSort = 50;

    public $vendor;
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('vendor');
    }
    
    public function mount(): void
    {
        // Redirect if user is not a vendor
        if (!auth()->user()->hasRole('vendor')) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }

        $vendorService = app(VendorService::class);
        $this->vendor = $vendorService->getVendorForUser(auth()->user());

        if (!$this->vendor) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }
    }
    
    public function reportsTable(Table $table): Table
    {
        return $table
            ->query(
                VendorMinistryReport::query()
                    ->where('vendor_id', $this->vendor->id)
                    ->orderBy('report_date', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Hotel')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('report_date')
                    ->label('Report Date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('report_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'submitted',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('submission_reference')
                    ->label('Reference')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('report_type')
                    ->options(config('vendor.ministry_report_types')),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['from_date']) {
                            $query->where('report_date', '>=', $data['from_date']);
                        }
                        
                        if ($data['to_date']) {
                            $query->where('report_date', '<=', $data['to_date']);
                        }
                        
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->visible(fn (VendorMinistryReport $record): bool => !empty($record->file_path))
                    ->url(fn (VendorMinistryReport $record): string => Storage::url($record->file_path))
                    ->openUrlInNewTab(),
                
                Tables\Actions\Action::make('submit')
                    ->label('Submit to Ministry')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (VendorMinistryReport $record): bool => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->maxLength(500),
                    ])
                    ->action(function (VendorMinistryReport $record, array $data): void {
                        $vendorService = app(VendorService::class);
                        $vendorService->submitMinistryReport(
                            $record,
                            $data['notes'] ?? null,
                            auth()->id()
                        );
                        
                        Notification::make()
                            ->title('Report submitted to ministry')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (VendorMinistryReport $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('submitBulk')
                        ->label('Submit Selected')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $vendorService = app(VendorService::class);
                            
                            // Only submit pending reports
                            $records = $records->filter(fn (VendorMinistryReport $record) => $record->status === 'pending');
                            
                            foreach ($records as $record) {
                                $vendorService->submitMinistryReport(
                                    $record,
                                    null,
                                    auth()->id()
                                );
                            }
                            
                            Notification::make()
                                ->title('Reports submitted to ministry')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
    
    public function createReportForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Create Ministry Report')
                    ->description('Create a new report for the ministry')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('hotel_id')
                                    ->label('Hotel')
                                    ->options(function () {
                                        return $this->vendor->hotels()
                                            ->where('is_active', true)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->required(),
                                
                                Forms\Components\Select::make('report_type')
                                    ->label('Report Type')
                                    ->options(config('vendor.ministry_report_types'))
                                    ->required(),
                                
                                Forms\Components\DatePicker::make('report_date')
                                    ->label('Report Date')
                                    ->required()
                                    ->default(now()),
                                
                                Forms\Components\FileUpload::make('file')
                                    ->label('Report File')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                    ->maxSize(10240) // 10MB
                                    ->columnSpanFull(),
                                
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes')
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('create')
                                ->label('Create Report')
                                ->submit()
                                ->icon('heroicon-o-plus')
                                ->color('primary')
                                ->action(function (array $data) {
                                    $this->createReport($data);
                                }),
                        ]),
                    ]),
            ]);
    }
    
    public function createReport(array $data): void
    {
        try {
            $vendorService = app(VendorService::class);
            
            // Prepare the report data
            $reportData = [
                'report_type' => $data['report_type'],
                'hotel_id' => $data['hotel_id'],
                'notes' => $data['notes'] ?? null,
            ];
            
            // Create the ministry report
            $report = $vendorService->createMinistryReport(
                $this->vendor,
                $data['hotel_id'],
                $data['report_type'],
                $data['report_date'],
                $reportData,
                $data['notes'] ?? null,
                auth()->id()
            );
            
            // Upload the file if provided
            if (isset($data['file'])) {
                // Get the file from the temporary storage
                $file = Storage::disk('local')->get('livewire-tmp/' . $data['file']);
                $originalName = pathinfo($data['file'], PATHINFO_FILENAME);
                $extension = pathinfo($data['file'], PATHINFO_EXTENSION);
                
                // Store the file
                $path = Storage::putFileAs(
                    'ministry_reports/' . $this->vendor->id,
                    new \Illuminate\Http\UploadedFile(
                        tempnam(sys_get_temp_dir(), 'upload'),
                        $originalName . '.' . $extension,
                        Storage::mimeType('livewire-tmp/' . $data['file']),
                        null,
                        true
                    ),
                    $report->id . '_' . date('Ymd') . '.' . $extension
                );
                
                // Update the report with the file path
                $report->update(['file_path' => $path]);
                
                // Delete temporary file
                Storage::delete('livewire-tmp/' . $data['file']);
            }
            
            Notification::make()
                ->title('Ministry report created successfully')
                ->success()
                ->send();
                
            $this->reset('data');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to create ministry report')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}