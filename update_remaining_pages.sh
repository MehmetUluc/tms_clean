#!/bin/bash

cd /var/www/app/Plugins/Partner/Filament/Pages

# Update PartnerDocuments.php
sed -i 's/namespace App\\Plugins\\Vendor\\Filament\\Pages;/namespace App\\Plugins\\Partner\\Filament\\Pages;/g' PartnerDocuments.php
sed -i 's/use App\\Plugins\\Vendor\\Models\\Vendor;/use App\\Plugins\\Partner\\Models\\Partner;/g' PartnerDocuments.php
sed -i 's/use App\\Plugins\\Vendor\\Models\\VendorDocument;/use App\\Plugins\\Partner\\Models\\PartnerDocument;/g' PartnerDocuments.php
sed -i 's/use App\\Plugins\\Vendor\\Services\\VendorService;/use App\\Plugins\\Partner\\Services\\VendorService;/g' PartnerDocuments.php
sed -i 's/class VendorDocuments/class PartnerDocuments/g' PartnerDocuments.php
sed -i 's/protected static string $view = .filament\.pages\.vendor-documents.;/protected static string $view = "filament.pages.partner-documents";/g' PartnerDocuments.php
sed -i 's/protected static ?string $title = .Vendor Documents.;/protected static ?string $title = "Partner Documents";/g' PartnerDocuments.php
sed -i 's/protected static ?string $navigationGroup = .Vendor.;/protected static ?string $navigationGroup = "Partner";/g' PartnerDocuments.php
sed -i 's/public $vendor;/public $partner;/g' PartnerDocuments.php
sed -i 's/public static function shouldRegisterNavigation(): bool\n    {\n        return auth()->user()->hasRole(.vendor.);\n    }/public static function shouldRegisterNavigation(): bool\n    {\n        return auth()->user()->hasRole("partner");\n    }/g' PartnerDocuments.php
sed -i 's/if (!auth()->user()->hasRole(.vendor.))/if (!auth()->user()->hasRole("partner"))/g' PartnerDocuments.php
sed -i 's/$this->vendor = $vendorService->getVendorForUser(auth()->user());/$this->partner = $vendorService->getVendorForUser(auth()->user());/g' PartnerDocuments.php
sed -i 's/if (!$this->vendor)/if (!$this->partner)/g' PartnerDocuments.php
sed -i 's/VendorDocument::query()\n            ->where(.vendor_id., $this->vendor->id)/PartnerDocument::query()\n            ->where("partner_id", $this->partner->id)/g' PartnerDocuments.php

# Update PartnerHotels.php
sed -i 's/namespace App\\Plugins\\Vendor\\Filament\\Pages;/namespace App\\Plugins\\Partner\\Filament\\Pages;/g' PartnerHotels.php
sed -i 's/use App\\Plugins\\Vendor\\Models\\Vendor;/use App\\Plugins\\Partner\\Models\\Partner;/g' PartnerHotels.php
sed -i 's/use App\\Plugins\\Vendor\\Services\\VendorService;/use App\\Plugins\\Partner\\Services\\VendorService;/g' PartnerHotels.php
sed -i 's/class VendorHotels/class PartnerHotels/g' PartnerHotels.php
sed -i 's/protected static string $view = .filament\.pages\.vendor-hotels.;/protected static string $view = "filament.pages.partner-hotels";/g' PartnerHotels.php
sed -i 's/protected static ?string $title = .Vendor Hotels.;/protected static ?string $title = "Partner Hotels";/g' PartnerHotels.php
sed -i 's/protected static ?string $navigationGroup = .Vendor.;/protected static ?string $navigationGroup = "Partner";/g' PartnerHotels.php
sed -i 's/public $vendor;/public $partner;/g' PartnerHotels.php
sed -i 's/public static function shouldRegisterNavigation(): bool\n    {\n        return auth()->user()->hasRole(.vendor.);\n    }/public static function shouldRegisterNavigation(): bool\n    {\n        return auth()->user()->hasRole("partner");\n    }/g' PartnerHotels.php
sed -i 's/if (!auth()->user()->hasRole(.vendor.))/if (!auth()->user()->hasRole("partner"))/g' PartnerHotels.php
sed -i 's/$this->vendor = $vendorService->getVendorForUser(auth()->user());/$this->partner = $vendorService->getVendorForUser(auth()->user());/g' PartnerHotels.php
sed -i 's/if (!$this->vendor)/if (!$this->partner)/g' PartnerHotels.php
sed -i 's/$this->vendor->hotels()/$this->partner->hotels()/g' PartnerHotels.php

# Update PartnerMinistryReporting.php
sed -i 's/namespace App\\Plugins\\Vendor\\Filament\\Pages;/namespace App\\Plugins\\Partner\\Filament\\Pages;/g' PartnerMinistryReporting.php
sed -i 's/use App\\Plugins\\Vendor\\Models\\Vendor;/use App\\Plugins\\Partner\\Models\\Partner;/g' PartnerMinistryReporting.php
sed -i 's/use App\\Plugins\\Vendor\\Models\\VendorMinistryReport;/use App\\Plugins\\Partner\\Models\\PartnerMinistryReport;/g' PartnerMinistryReporting.php
sed -i 's/use App\\Plugins\\Vendor\\Services\\VendorService;/use App\\Plugins\\Partner\\Services\\VendorService;/g' PartnerMinistryReporting.php
sed -i 's/class VendorMinistryReporting/class PartnerMinistryReporting/g' PartnerMinistryReporting.php
sed -i 's/protected static string $view = .filament\.pages\.vendor-ministry-reporting.;/protected static string $view = "filament.pages.partner-ministry-reporting";/g' PartnerMinistryReporting.php
sed -i 's/protected static ?string $title = .Ministry Reports.;/protected static ?string $title = "Ministry Reports";/g' PartnerMinistryReporting.php
sed -i 's/protected static ?string $navigationGroup = .Vendor.;/protected static ?string $navigationGroup = "Partner";/g' PartnerMinistryReporting.php
sed -i 's/public $vendor;/public $partner;/g' PartnerMinistryReporting.php
sed -i 's/public static function shouldRegisterNavigation(): bool\n    {\n        return auth()->user()->hasRole(.vendor.);\n    }/public static function shouldRegisterNavigation(): bool\n    {\n        return auth()->user()->hasRole("partner");\n    }/g' PartnerMinistryReporting.php
sed -i 's/if (!auth()->user()->hasRole(.vendor.))/if (!auth()->user()->hasRole("partner"))/g' PartnerMinistryReporting.php
sed -i 's/$this->vendor = $vendorService->getVendorForUser(auth()->user());/$this->partner = $vendorService->getVendorForUser(auth()->user());/g' PartnerMinistryReporting.php
sed -i 's/if (!$this->vendor)/if (!$this->partner)/g' PartnerMinistryReporting.php
sed -i 's/VendorMinistryReport::query()\n            ->where(.vendor_id., $this->vendor->id)/PartnerMinistryReport::query()\n            ->where("partner_id", $this->partner->id)/g' PartnerMinistryReporting.php

echo "All remaining page files updated successfully!"