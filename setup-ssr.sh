#!/bin/bash

# Setup Inertia SSR for Laravel

# Build SSR assets
echo "Building SSR assets..."
npm run build
npm run build:ssr

# Add SSR configuration to .env
if ! grep -q "APP_SSR_ENABLED" .env; then
    echo "" >> .env
    echo "# Inertia SSR Configuration" >> .env
    echo "APP_SSR_ENABLED=true" >> .env
    echo "APP_SSR_PORT=13714" >> .env
    echo "Added SSR configuration to .env file"
fi

# Start SSR server
echo "Starting SSR server..."
php artisan inertia:start-ssr

echo "SSR setup complete!"