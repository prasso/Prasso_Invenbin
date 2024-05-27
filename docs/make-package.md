### Notes on Creating a Package with Filament Support

Creating an add-in (or package) for a Laravel project involves several steps. This can help in organizing your code, making it reusable, and simplifying future maintenance. Here are the detailed steps and suggestions to create a Laravel package:

#### 1. Set Up a New Package Directory

Create a new directory for your package. This directory can be placed in the `packages` directory of your Laravel project.

```bash
mkdir -p packages/YourVendorName/YourPackageName
cd packages/YourVendorName/YourPackageName
```

#### 2. Create a `composer.json` File

Initialize your package with a `composer.json` file. This file will define the package's dependencies and metadata.

```json
{
    "name": "your-vendor-name/your-package-name",
    "description": "A description of your package",
    "type": "library",
    "require": {
        "php": ">=7.4",
        "illuminate/support": "^8.0"
    },
    "autoload": {
        "psr-4": {
            "YourVendorName\\YourPackageName\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "YourVendorName\\YourPackageName\\YourPackageServiceProvider"
            ]
        }
    }
}
```

#### 3. Create the Package Structure

Create the necessary directories and files for your package.

```bash
mkdir -p src
touch src/YourPackageServiceProvider.php
```

#### 4. Implement the Service Provider

The service provider is where you register your package's services with the Laravel application.

```php
namespace YourVendorName\YourPackageName;

use Illuminate\Support\ServiceProvider;

class YourPackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/yourpackage.php' => config_path('yourpackage.php'),
        ]);

        // Load routes
        if (file_exists(__DIR__.'/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // Load views
        if (is_dir(__DIR__.'/../resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'yourpackage');
        }

        // Load migrations
        if (is_dir(__DIR__.'/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/yourpackage.php', 'yourpackage'
        );

        // Register any application services.
    }
}
```

#### 5. Add Additional Files (Configuration, Routes, Views, Migrations)

Create additional files as needed.

**Configuration file:**

```bash
mkdir -p config
touch config/yourpackage.php
```

**Routes file:**

```bash
mkdir -p routes
touch routes/web.php
```

**Views directory:**

```bash
mkdir -p resources/views
```

**Migrations directory:**

```bash
mkdir -p database/migrations
```

#### 6. Link the Package in Your Laravel Project

To use your package, you need to add the repository to your `composer.json` of your Laravel project.

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/YourVendorName/YourPackageName"
    }
]
```

Then require your package:

```bash
composer require your-vendor-name/your-package-name
```

#### 7. Register the Service Provider

In some cases, Laravel will auto-discover the service provider. If not, you can add it manually to the `config/app.php` file.

```php
'providers' => [
    // Other Service Providers

    YourVendorName\YourPackageName\YourPackageServiceProvider::class,
],
```

#### 8. Publish the Package Assets

If you have assets to publish (like configuration files), you can use the `vendor:publish` command.

```bash
php artisan vendor:publish --provider="YourVendorName\YourPackageName\YourPackageServiceProvider"
```

#### 9. Test Your Package

Ensure your package works as expected by testing it within your Laravel application.

**Example Package Structure:**

Your final package directory might look like this:

```
packages/
└── YourVendorName/
    └── YourPackageName/
        ├── config/
        │   └── yourpackage.php
        ├── database/
        │   └── migrations/
        ├── resources/
        │   └── views/
        ├── routes/
        │   └── web.php
        ├── src/
        │   └── YourPackageServiceProvider.php
        └── composer.json
```

By following these steps, you can successfully create a Laravel package that encapsulates your custom code, making it easier to manage and reuse across different projects.

### Support for Filament in a Package

To add support for Filament in your Laravel package, follow these additional steps:

1. **Place Filament Resources**: Create a directory `src/Filament/Resources` in your package, and place your Filament Resources (e.g., Pages, Widgets) in this directory. The namespace for these resources should be `YourVendorName\YourPackageName\Filament\Resources\`.

2. **Create a Facade File**: Create a facade file for your Filament Panel in `src/Support/Facades`. This facade will have one method that identifies the panel used by the package to the application. The method should look like this:

   ```php
   protected static function getFacadeAccessor(): string
   {
       return 'invenbin';
   }
   ```

3. **Create a PanelManager**: Create a `PanelManager` class at the same level as the `PackageServiceProvider` in the `src` directory (e.g., `packages/faxt/invenbin/src/InvenbinPanelManager.php`).

4. **Register the PanelManager**: In the `register` method of your `PackageServiceProvider`, add the `PanelManager` to the application's container:

   ```php
   $this->app->scoped('invenbin', function (): InvenbinPanelManager {
       return new InvenbinPanelManager();
   });
   ```

5. **Register the Panel**: In your application's `AppServiceProvider` (`app\AppServiceProvider`), register your panel by calling the `register` method:

   ```php
   public function register()
   {
       InvenbinPanel::register();
   }
   ```

By following these additional steps, you can integrate Filament support into your Laravel package, allowing you to create and register Filament Resources and Panels within your package.