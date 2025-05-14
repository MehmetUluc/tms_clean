#!/bin/bash

# Fix the remaining issues in all Partner files

cd /var/www/app/Plugins/Partner/Filament/Pages

# Fix shouldRegisterNavigation in all files
sed -i 's/return auth()->user()->hasRole(.vendor.);/return auth()->user()->hasRole("partner");/g' PartnerDocuments.php
sed -i 's/return auth()->user()->hasRole(.vendor.);/return auth()->user()->hasRole("partner");/g' PartnerHotels.php
sed -i 's/return auth()->user()->hasRole(.vendor.);/return auth()->user()->hasRole("partner");/g' PartnerMinistryReporting.php

# Fix references from vendor to partner
sed -i 's/$this->vendor->/$this->partner->/g' PartnerDocuments.php
sed -i 's/$this->vendor->/$this->partner->/g' PartnerHotels.php
sed -i 's/$this->vendor->/$this->partner->/g' PartnerMinistryReporting.php

# Fix partner_id and vendor_id usage in all files
sed -i 's/->where(.vendor_id., /->where("partner_id", /g' PartnerDocuments.php
sed -i 's/->where(.vendor_id., /->where("partner_id", /g' PartnerHotels.php
sed -i 's/->where(.vendor_id., /->where("partner_id", /g' PartnerMinistryReporting.php

# Fix specific references to VendorDocument, VendorMinistryReport classes
sed -i 's/VendorDocument /PartnerDocument /g' PartnerDocuments.php
sed -i 's/VendorDocument::/PartnerDocument::/g' PartnerDocuments.php
sed -i 's/VendorMinistryReport /PartnerMinistryReport /g' PartnerMinistryReporting.php
sed -i 's/VendorMinistryReport::/PartnerMinistryReport::/g' PartnerMinistryReporting.php

# Fix config references from vendor to partner
sed -i 's/config(.vendor\./config("partner\./g' PartnerDocuments.php
sed -i 's/config(.vendor\./config("partner\./g' PartnerHotels.php
sed -i 's/config(.vendor\./config("partner\./g' PartnerMinistryReporting.php

# Update vendor_id in PartnerHotels.php where it's used for creating a hotel
sed -i 's/\.create', \[\x27vendor_id\x27 => \$this->vendor->id\])/\.create', \['partner_id' => \$this->partner->id\])/g' PartnerHotels.php

echo "All Partner files have been updated successfully!"