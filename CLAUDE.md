# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Travel Management System (TMS) built with Laravel and FilamentPHP. It uses a modular plugin-based architecture with multi-tenant capabilities. The system is designed for hotel and travel management with features for accommodation, booking, amenities, and more.

## Development Commands

### Installation and Setup

```bash
# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Set up database
php artisan migrate
php artisan db:seed

# Create admin user
php artisan tms:create-admin

# Option 1: Setup minimal clean system for client use
php artisan tms:setup-clean

# Option 2: Seed specific required data
php artisan tms:seed-board-types

# Set up file system
php artisan storage:link
```

### Development Commands

```bash
# Start development server
php artisan serve
npm run dev

# Create a new plugin
php artisan make:filament-plugin PluginName

# Set up region hierarchy
php artisan setup:region-hierarchy

# Reset region hierarchy
php artisan setup:region-hierarchy --fresh

# Test plugin structure integrity
php php_test.php

# Set up permissions
php artisan setup:permissions

# Seed board types (required for Pricing Management)
php artisan tms:seed-board-types
```

## Architecture

### Plugin-Based Architecture

The application is organized into modular plugins, each handling specific functionality:

1. **Core Plugin**: Foundation services, common traits, shared components
2. **Accommodation Plugin**: Hotels, rooms, and room types management
3. **Booking Plugin**: Reservations and guest management
4. **Amenities Plugin**: Hotel and room amenities/facilities management
5. **Integration Plugin**: API connections and data mapping
6. **UserManagement Plugin**: User, role, and permission management
7. **Pricing Plugin**: Rate plans, pricing management
8. **OTA Plugin**: Online Travel Agency integrations
9. **ThemeManager Plugin**: UI theming and customization

Each plugin follows a standard structure:
```
app/Plugins/PluginName/
├── PluginNamePlugin.php         # Main plugin class
├── PluginNameServiceProvider.php # Laravel service provider
├── config/                      # Plugin configuration
├── database/                    # Migrations
│   └── migrations/
├── Filament/                    # Filament resources, pages, widgets
│   ├── Pages/
│   ├── Resources/
│   └── Widgets/
├── Models/                      # Plugin-specific models
└── resources/                   # Views, CSS, JS
    └── views/
```

### Multi-Tenant Architecture

The system implements multi-tenant capabilities at the database level. All models automatically filter by tenant, implemented through the `HasTenant` trait. Each operation is specific to the selected tenant (agency).

### Region Hierarchy

The system uses a hierarchical region structure with:
- Countries (e.g., Turkey)
- Regions (e.g., Mediterranean Region)
- Cities (e.g., Antalya)
- Districts (e.g., Konyaaltı)

This structure is implemented through a self-referencing relationship in the Region model, with parent-child relationships.

### Booking Wizard

The booking wizard provides a step-by-step interface for creating reservations:
1. Region selection
2. Date and guest information
3. Hotel selection based on availability
4. Room and board type selection
5. Price calculation and booking confirmation

### Pricing Management

The pricing model supports:
- Rate plans for different room and board type combinations
- Date-based pricing with periods and exceptions
- Per-person or per-unit pricing
- Inventory management
- Minimum stay requirements

> **Important:** The Pricing Management system requires board types to be available in the database. When setting up a fresh system, make sure to run `php artisan tms:seed-board-types` before using the pricing features.

## Code Patterns

### Repository Pattern

The system uses Repository pattern for data access layer, isolating the data persistence logic.

### Service Layer

Business logic is organized into service classes following single responsibility principle.

### Contract-based Programming

The system uses interfaces (contracts) to reduce tight coupling between components.

### Event-driven Architecture

Events and listeners are used for scalability and to decouple system components.

## Database Structure

Key tables in the system include:

- `users` - User accounts
- `regions` - Hierarchical locations structure
- `hotels` - Hotel properties
- `rooms` - Physical rooms
- `room_types` - Room categories
- `board_types` - Meal plan options
- `reservations` - Booking information
- `guests` - Guest details
- `rate_plans` - Pricing structures
- `rate_periods` - Date-range pricing
- `rate_exceptions` - Date-specific pricing exceptions

## Important Features

### Plugin Management

Plugins are registered in the Filament Admin Panel Provider:

```php
// In app/Providers/Filament/AdminPanelProvider.php
->plugins([
    App\Plugins\Core\CorePlugin::make(),
    App\Plugins\Accommodation\AccommodationPlugin::make(),
    App\Plugins\Booking\BookingPlugin::make(),
    // Other plugins...
])
```

### OTA Integration

The system provides integration with Online Travel Agencies through:
- XML/JSON data mapping
- Webhook endpoints
- Data transformation templates
- Two-way data flow (import/export)

### Theme Management

The ThemeManager plugin allows customization of the UI:
- Color schemes
- Typography
- Component styling
- Layout customization

## Testing

The system includes tests for plugins and core functionality:

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/BookingPluginTest.php
```