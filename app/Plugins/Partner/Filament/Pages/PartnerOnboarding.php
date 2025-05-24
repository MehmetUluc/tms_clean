<?php

namespace App\Plugins\Partner\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use App\Plugins\Partner\Models\Partner;
use App\Plugins\Partner\Services\PartnerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerOnboarding extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Partner Onboarding';
    protected static ?string $slug = 'partner-onboarding';
    protected static ?string $title = 'Partner Kayıt ve Sözleşme';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.partner-onboarding';
    
    public ?array $data = [];
    
    protected function getViewData(): array
    {
        $partner = Partner::where('user_id', Auth::id())->first();
        
        return [
            'partner' => $partner,
            'agreementText' => $this->getAgreementText(),
        ];
    }
    
    public function mount(): void
    {
        $partner = Partner::where('user_id', Auth::id())->first();
        
        if ($partner && $partner->onboarding_completed) {
            // Eğer onboarding tamamlanmışsa dashboard'a yönlendir
            redirect('/partner/partner-dashboard');
        }
        
        if ($partner) {
            $this->form->fill([
                'company_name' => $partner->company_name,
                'tax_number' => $partner->tax_number,
                'tax_office' => $partner->tax_office,
                'phone' => $partner->phone,
                'address' => $partner->address,
                'city' => $partner->city,
                'country' => $partner->country,
                'postal_code' => $partner->postal_code,
                'website' => $partner->website,
                'contact_person' => $partner->contact_person,
                'contact_email' => $partner->contact_email,
                'contact_phone' => $partner->contact_phone,
                'tourism_certificate_number' => $partner->tourism_certificate_number,
                'tourism_certificate_valid_until' => $partner->tourism_certificate_valid_until,
            ]);
        }
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Şirket Bilgileri')
                        ->description('Şirket bilgilerinizi girin')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('company_name')
                                        ->label('Şirket Adı')
                                        ->required()
                                        ->maxLength(255),
                                        
                                    TextInput::make('tax_number')
                                        ->label('Vergi Numarası')
                                        ->required()
                                        ->maxLength(20),
                                        
                                    TextInput::make('tax_office')
                                        ->label('Vergi Dairesi')
                                        ->required()
                                        ->maxLength(100),
                                        
                                    TextInput::make('phone')
                                        ->label('Telefon')
                                        ->tel()
                                        ->required()
                                        ->maxLength(20),
                                ]),
                        ]),
                        
                    Wizard\Step::make('Adres Bilgileri')
                        ->description('Adres bilgilerinizi girin')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Textarea::make('address')
                                        ->label('Adres')
                                        ->required()
                                        ->rows(3)
                                        ->columnSpan('full'),
                                        
                                    TextInput::make('city')
                                        ->label('Şehir')
                                        ->required()
                                        ->maxLength(100),
                                        
                                    Select::make('country')
                                        ->label('Ülke')
                                        ->options([
                                            'TR' => 'Türkiye',
                                            'DE' => 'Almanya',
                                            'GB' => 'İngiltere',
                                            'US' => 'Amerika',
                                        ])
                                        ->default('TR')
                                        ->required(),
                                        
                                    TextInput::make('postal_code')
                                        ->label('Posta Kodu')
                                        ->maxLength(10),
                                        
                                    TextInput::make('website')
                                        ->label('Web Sitesi')
                                        ->url()
                                        ->prefix('https://')
                                        ->maxLength(255),
                                ]),
                        ]),
                        
                    Wizard\Step::make('İletişim Bilgileri')
                        ->description('İletişim bilgilerinizi girin')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('contact_person')
                                        ->label('Yetkili Kişi')
                                        ->required()
                                        ->maxLength(100),
                                        
                                    TextInput::make('contact_email')
                                        ->label('Yetkili E-posta')
                                        ->email()
                                        ->required()
                                        ->maxLength(100),
                                        
                                    TextInput::make('contact_phone')
                                        ->label('Yetkili Telefon')
                                        ->tel()
                                        ->required()
                                        ->maxLength(20),
                                ]),
                        ]),
                        
                    Wizard\Step::make('Turizm Belgesi')
                        ->description('Turizm işletme belgesi bilgilerinizi girin')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('tourism_certificate_number')
                                        ->label('Turizm İşletme Belgesi Numarası')
                                        ->required()
                                        ->helperText('Turizm Bakanlığı tarafından verilen belge numarası')
                                        ->maxLength(50),
                                        
                                    DatePicker::make('tourism_certificate_valid_until')
                                        ->label('Belge Geçerlilik Tarihi')
                                        ->required()
                                        ->minDate(now())
                                        ->helperText('Belgenizin son geçerlilik tarihi'),
                                ]),
                        ]),
                        
                    Wizard\Step::make('Sözleşme Onayı')
                        ->description('Partner sözleşmesini onaylayın')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Section::make('Partner Sözleşmesi')
                                ->description(fn () => new \Illuminate\Support\HtmlString($this->getAgreementText()))
                                ->schema([
                                    Checkbox::make('agreement_accepted')
                                        ->label('Partner sözleşmesini okudum ve kabul ediyorum')
                                        ->required()
                                        ->accepted()
                                        ->helperText('Sözleşmeyi kabul etmeden devam edemezsiniz.'),
                                ]),
                        ]),
                ])
                ->submitAction($this->getSubmitFormAction())
                ->startOnStep(1)
            ])
            ->statePath('data');
    }
    
    protected function getSubmitFormAction(): Action
    {
        return Action::make('submit')
            ->label('Kaydı Tamamla')
            ->submit('submit');
    }
    
    public function submit(): void
    {
        $data = $this->form->getState();
        
        DB::beginTransaction();
        
        try {
            $partnerService = app(PartnerService::class);
            
            // Partner kaydını bul veya oluştur
            $partner = Partner::where('user_id', Auth::id())->first();
            
            if (!$partner) {
                $partner = $partnerService->createPartner(
                    array_merge($data, ['user_id' => Auth::id()]),
                    Auth::user()
                );
            } else {
                // Mevcut partner kaydını güncelle
                $partner->update($data);
            }
            
            // Sözleşmeyi kabul et
            if ($data['agreement_accepted']) {
                $partner->acceptAgreement();
            }
            
            // Onboarding'i tamamla
            $partner->completeOnboarding();
            
            // Partner rolünü ata
            Auth::user()->assignRole('partner');
            
            DB::commit();
            
            Notification::make()
                ->title('Kayıt Başarılı')
                ->body('Partner kaydınız başarıyla tamamlandı. Artık sistemi kullanabilirsiniz.')
                ->success()
                ->send();
                
            // Dashboard'a yönlendir
            redirect('/partner/partner-dashboard');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Hata')
                ->body('Kayıt sırasında bir hata oluştu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    protected function getAgreementText(): string
    {
        return <<<HTML
        <div class="prose prose-sm max-w-none">
            <h3>TURİSTİK TMS PARTNER SÖZLEŞMESİ</h3>
            
            <h4>1. TARAFLAR</h4>
            <p>İşbu sözleşme, Turistik TMS ("Platform") ile Partner ("İş Ortağı") arasında aşağıdaki şartlar dahilinde akdedilmiştir.</p>
            
            <h4>2. SÖZLEŞMENİN KONUSU</h4>
            <p>Bu sözleşmenin konusu, İş Ortağı'nın sahip olduğu veya işlettiği otel ve konaklama tesislerinin Platform üzerinden pazarlanması, rezervasyonlarının alınması ve yönetilmesidir.</p>
            
            <h4>3. TARAFLARIN YÜKÜMLÜLÜKLERİ</h4>
            
            <h5>3.1. İş Ortağı'nın Yükümlülükleri:</h5>
            <ul>
                <li>Geçerli turizm işletme belgesine sahip olmak</li>
                <li>Doğru ve güncel bilgiler sağlamak</li>
                <li>Fiyat ve müsaitlik bilgilerini güncel tutmak</li>
                <li>Misafirlere taahhüt edilen hizmetleri sunmak</li>
                <li>Platform kurallarına uymak</li>
            </ul>
            
            <h5>3.2. Platform'un Yükümlülükleri:</h5>
            <ul>
                <li>Güvenli ve kesintisiz hizmet sağlamak</li>
                <li>Rezervasyonları zamanında iletmek</li>
                <li>Teknik destek sağlamak</li>
                <li>Ödeme işlemlerini yönetmek</li>
            </ul>
            
            <h4>4. KOMİSYON VE ÖDEMELER</h4>
            <p>Platform, gerçekleşen rezervasyonlardan %15 komisyon alır. Ödemeler aylık olarak yapılır.</p>
            
            <h4>5. GİZLİLİK</h4>
            <p>Taraflar, işbu sözleşme kapsamında öğrendikleri ticari sırları ve gizli bilgileri korumayı taahhüt eder.</p>
            
            <h4>6. SÖZLEŞMENİN SÜRESİ</h4>
            <p>Bu sözleşme imza tarihinden itibaren 1 yıl sürelidir ve taraflarca feshedilmediği sürece otomatik olarak yenilenir.</p>
            
            <h4>7. UYUŞMAZLIKLARIN ÇÖZÜMÜ</h4>
            <p>Bu sözleşmeden doğan uyuşmazlıklarda İstanbul Mahkemeleri ve İcra Daireleri yetkilidir.</p>
        </div>
        HTML;
    }
    
    public static function canAccess(): bool
    {
        // Partner rolü olan veya henüz onboarding tamamlamamış kullanıcılar erişebilir
        return Auth::user()->hasRole('partner') || 
               (Auth::user()->partner && !Auth::user()->partner->onboarding_completed);
    }
}