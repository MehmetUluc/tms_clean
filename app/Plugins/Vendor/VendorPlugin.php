<?php

namespace App\Plugins\Vendor;

use App\Plugins\Vendor\Filament\Resources\VendorResource;
use App\Plugins\Vendor\Filament\Pages\VendorDashboard;
use App\Plugins\Vendor\Filament\Pages\FinancialSummary;
use App\Plugins\Vendor\Filament\Pages\VendorDocuments;
use App\Plugins\Vendor\Filament\Pages\VendorHotels;
use App\Plugins\Vendor\Filament\Pages\VendorMinistryReporting;
use App\Plugins\Vendor\Filament\Pages\TestPage;
use App\Plugins\Vendor\Filament\Pages\SimpleTestPage;
use App\Plugins\Vendor\Filament\Pages\StaticTestPage;
use App\Plugins\Vendor\Filament\Pages\BasicTestPage;
use App\Plugins\Vendor\Filament\Pages\SimplifiedFinancialSummary;
use App\Plugins\Vendor\Filament\Pages\ForcedTestPage;
use App\Plugins\Vendor\Filament\Pages\NewFinancialSummary;
use App\Plugins\Vendor\Filament\Pages\PlainFinancialSummary;
use App\Plugins\Vendor\Filament\Pages\FinancialSummaryFixed;
use Filament\Contracts\Plugin;
use Filament\Panel;

class VendorPlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'vendor';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                VendorResource::class,
            ])
            ->pages([
                VendorDashboard::class,
                FinancialSummary::class,
                VendorDocuments::class,
                VendorHotels::class,
                VendorMinistryReporting::class,
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