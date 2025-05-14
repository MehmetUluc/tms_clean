<?php

namespace App\Plugins\Partner;

use App\Plugins\Partner\Filament\Resources\PartnerResource;
use App\Plugins\Partner\Filament\Pages\PartnerDashboard;
use App\Plugins\Partner\Filament\Pages\FinancialSummary;
use App\Plugins\Partner\Filament\Pages\PartnerDocuments;
use App\Plugins\Partner\Filament\Pages\PartnerHotels;
use App\Plugins\Partner\Filament\Pages\PartnerMinistryReporting;
use App\Plugins\Partner\Filament\Pages\TestPage;
use App\Plugins\Partner\Filament\Pages\SimpleTestPage;
use App\Plugins\Partner\Filament\Pages\StaticTestPage;
use App\Plugins\Partner\Filament\Pages\BasicTestPage;
use App\Plugins\Partner\Filament\Pages\SimplifiedFinancialSummary;
use App\Plugins\Partner\Filament\Pages\ForcedTestPage;
use App\Plugins\Partner\Filament\Pages\NewFinancialSummary;
use App\Plugins\Partner\Filament\Pages\PlainFinancialSummary;
use App\Plugins\Partner\Filament\Pages\FinancialSummaryFixed;
use Filament\Contracts\Plugin;
use Filament\Panel;

class PartnerPlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'partner';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                PartnerResource::class,
            ])
            ->pages([
                PartnerDashboard::class,
                FinancialSummary::class,
                PartnerDocuments::class,
                PartnerHotels::class,
                PartnerMinistryReporting::class,
                TestPage::class,
                SimpleTestPage::class,
                StaticTestPage::class,
                BasicTestPage::class,
                SimplifiedFinancialSummary::class,
                ForcedTestPage::class,
                NewFinancialSummary::class,
                PlainFinancialSummary::class,
                FinancialSummaryFixed::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}