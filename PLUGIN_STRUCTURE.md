# Filament Plugin Structure

This document outlines the structure and organization of the modular plugin-based architecture implemented in this project.

## Overview

The plugin architecture organizes code into logical, isolated components that can be enabled, disabled, and managed independently. This approach improves maintainability, testability, and enables a more scalable development process.

Each plugin is a self-contained unit that includes:
- Models
- Filament Resources
- Pages
- Widgets
- Migrations
- Views
- Services
- Configuration

## Core Plugin

The Core plugin provides the foundation and common services used by all other plugins:

- Base classes
- Common traits
- Shared components
- Plugin management infrastructure

## Plugin Structure

Each plugin follows a standard structure:

```
app/Plugins/PluginName/
├── PluginNamePlugin.php         # Main plugin class implementing Plugin interface
├── PluginNameServiceProvider.php # Laravel service provider for the plugin
├── config/                      # Plugin configuration
├── database/                    # Migrations
│   └── migrations/
├── Filament/                    # Filament resources, pages, and widgets
│   ├── Pages/
│   ├── Resources/
│   └── Widgets/
├── Models/                      # Plugin-specific models
└── resources/                   # Views, CSS, JS
    └── views/
```

## Available Plugins

1. **Core** - Foundation services and components
2. **Accommodation** - Hotels, rooms, and room types management
3. **Booking** - Reservations and guest management
4. **Amenities** - Hotel and room amenities/facilities management
5. **Integration** - API connections and data mapping
6. **UserManagement** - User, role, and permission management

## Plugin Registration

Plugins are registered in the `AppServiceProvider` and loaded through the Filament Panel Provider:

```php
// In app/Providers/Filament/AdminPanelProvider.php
->plugins([
    App\Plugins\Core\CorePlugin::make(),
    App\Plugins\Accommodation\AccommodationPlugin::make(),
    App\Plugins\Booking\BookingPlugin::make(),
    App\Plugins\Amenities\AmenitiesPlugin::make(),
    App\Plugins\Integration\IntegrationPlugin::make(),
    App\Plugins\UserManagement\UserManagementPlugin::make(),
])
```

## Creating a New Plugin

To create a new plugin:

1. Create the plugin directory structure following the standard template
2. Create the main plugin class implementing the Plugin interface
3. Create a service provider for the plugin
4. Register the plugin in the `AdminPanelProvider`
5. Add resources, pages, and widgets to the plugin

Example of a basic plugin class:

```php
<?php

namespace App\Plugins\Example;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class ExamplePlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'example';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                Filament\Resources\ExampleResource::class,
            ])
            ->pages([
                Filament\Pages\ExamplePage::class,
            ])
            ->widgets([
                Filament\Widgets\ExampleWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot logic
    }
    
    public function isEnabled(): bool
    {
        return true;
    }
    
    public function getInfo(): array
    {
        return [
            'name' => 'Example',
            'description' => 'Example plugin for demonstration',
            'version' => '1.0.0',
            'author' => 'Filament',
        ];
    }
}
```

## Testing Plugins

The project includes a test script to verify plugin structure integrity:

```bash
php plugin_test.php
```

This will check that all plugin components are correctly set up and accessible.

## Best Practices

1. Keep plugins focused on a specific domain or functionality
2. Use the Core plugin for shared components and services
3. Maintain consistent naming conventions
4. Document plugin interfaces and extension points
5. Add comprehensive tests for each plugin
6. Use proper namespacing to avoid conflicts