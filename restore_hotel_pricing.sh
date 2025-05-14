#!/bin/bash

# Stop execution if any command fails
set -e

echo "Starting restoration of hotel pricing page..."

# Create backup of current files
echo "Creating backups of current files..."
cp -f "/var/www/app/Plugins/Pricing/Filament/Pages/HotelPricingPage.php" "/var/www/app/Plugins/Pricing/Filament/Pages/HotelPricingPage.php.broken"
cp -f "/var/www/resources/views/filament/pages/hotel-pricing-page.blade.php" "/var/www/resources/views/filament/pages/hotel-pricing-page.blade.php.broken"

# Restore the original working files
echo "Restoring original working files..."
cp -f "/var/www/app/Plugins/Pricing/Filament/Pages/HotelPricingPage.php.original" "/var/www/app/Plugins/Pricing/Filament/Pages/HotelPricingPage.php"
cp -f "/var/www/resources/views/filament/pages/hotel-pricing-page.blade.php.original" "/var/www/resources/views/filament/pages/hotel-pricing-page.blade.php"

# Set permissions
echo "Setting correct permissions..."
chmod 644 "/var/www/app/Plugins/Pricing/Filament/Pages/HotelPricingPage.php"
chmod 644 "/var/www/resources/views/filament/pages/hotel-pricing-page.blade.php"

echo "Clearing Laravel cache..."
php /var/www/artisan cache:clear
php /var/www/artisan view:clear

echo "Restoration complete!"
echo ""
echo "Original files have been backed up with .broken extension."
echo "The hotel pricing page has been restored to the original working version."