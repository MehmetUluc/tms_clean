<?php

namespace App\Plugins\Partner\Filament\Pages;

use App\Plugins\Partner\Models\PartnerDocument;
use App\Plugins\Partner\Services\PartnerService;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PartnerDocuments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = "filament.pages.partner-documents";
    
    protected static ?string $title = 'Documents';
    
    protected static ?string $navigationGroup = "Partner";
    
    protected static ?int $navigationSort = 40;

    public $partner;
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole("partner");
    }
    
    public function mount(): void
    {
        // Redirect if user is not a partner
        if (!auth()->user()->hasRole("partner")) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }

        $partnerService = app(PartnerService::class);
        $this->partner = $partnerService->getPartnerForUser(auth()->user());

        if (!$this->partner) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }
    }
    
    public function uploadForm(Form $form): Form
    {
        $documentTypes = config('partner.document_types', []);
        
        return $form
            ->schema([
                Forms\Components\Section::make('Upload Document')
                    ->description('Upload a new document for your partner account')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('document_type')
                                    ->label('Document Type')
                                    ->options($documentTypes)
                                    ->required(),
                                
                                Forms\Components\TextInput::make('name')
                                    ->label('Document Name')
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\FileUpload::make('file')
                                    ->label('Document File')
                                    ->required()
                                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                    ->maxSize(10240) // 10MB in KB
                                    ->columnSpanFull(),
                                
                                Forms\Components\DatePicker::make('expiry_date')
                                    ->label('Expiry Date (if applicable)'),
                                
                                Forms\Components\Textarea::make('comments')
                                    ->label('Comments')
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('upload')
                                ->label('Upload Document')
                                ->submit()
                                ->icon('heroicon-o-paper-airplane')
                                ->color('primary')
                                ->action(function (array $data) {
                                    $this->uploadDocument($data);
                                }),
                        ]),
                    ]),
            ]);
    }
    
    public function uploadDocument(array $data): void
    {
        try {
            $partnerService = app(PartnerService::class);
            
            // Get the file from the temporary storage
            $file = Storage::disk('local')->get('livewire-tmp/' . $data['file']);
            $originalName = pathinfo($data['file'], PATHINFO_FILENAME);
            $extension = pathinfo($data['file'], PATHINFO_EXTENSION);
            
            // Create a new UploadedFile instance
            $tempFile = tempnam(sys_get_temp_dir(), 'upload');
            file_put_contents($tempFile, $file);
            
            $uploadedFile = new UploadedFile(
                $tempFile,
                $originalName . '.' . $extension,
                Storage::mimeType('livewire-tmp/' . $data['file']),
                null,
                true
            );
            
            // Upload the document
            $partnerService->uploadDocument(
                $this->partner,
                $data['document_type'],
                $data['name'],
                $uploadedFile,
                $data['comments'] ?? null,
                $data['expiry_date'] ?? null,
                auth()->id()
            );
            
            // Delete temporary file
            Storage::delete('livewire-tmp/' . $data['file']);
            @unlink($tempFile);
            
            Notification::make()
                ->title('Document uploaded successfully')
                ->success()
                ->send();
                
            $this->reset('data');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to upload document')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function documentsTable(Table $table): Table
    {
        return $table
            ->query(
                PartnerDocument::query()
                    ->where("partner_id", $this->partner->id)
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Document Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Type')
                    ->formatStateUsing(function ($state) {
                        $documentTypes = config('partner.document_types', []);
                        return $documentTypes[$state] ?? ucfirst(str_replace('_', ' ', $state));
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Upload Date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_expired')
                    ->label('Expired')
                    ->boolean()
                    ->getStateUsing(fn (PartnerDocument $record): bool => $record->isExpired())
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                
                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('document_type')
                    ->options(config('partner.document_types', [])),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                
                Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(fn ($query) => $query->whereNotNull('expiry_date')->where('expiry_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (PartnerDocument $record): string => Storage::url($record->file_path))
                    ->openUrlInNewTab(),
                
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (PartnerDocument $record): string => Storage::url($record->file_path))
                    ->openUrlInNewTab(),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (PartnerDocument $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            // Only delete pending documents
                            $records = $records->filter(fn (PartnerDocument $record) => $record->status === 'pending');
                            
                            foreach ($records as $record) {
                                // Delete the file from storage
                                Storage::delete($record->file_path);
                                
                                // Delete the record
                                $record->delete();
                            }
                            
                            Notification::make()
                                ->title('Documents deleted successfully')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}