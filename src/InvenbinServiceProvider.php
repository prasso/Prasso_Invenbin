<?php

namespace Faxt\Invenbin;

use Illuminate\Support\ServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Facades\Filament;
use Faxt\Invenbin\Filament\Resources\ErpProductResource;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages;
use Faxt\Invenbin\Support\Facades\InvenbinPanel;
use Illuminate\Support\Facades\Log;


class InvenbinServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/invenbin.php' => config_path('invenbin.php'),
        ], 'config');

        // Load routes
        if (file_exists(__DIR__.'/../routes/api.php')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }

        // Load views
        if (is_dir(__DIR__.'/../resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'invenbin');
        }

        // Load migrations
        if (is_dir(__DIR__.'/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        Filament::registerResources(InvenbinPanel::getInvenbinResources());

        
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/invenbin.php', 'invenbin'
        );
        $this->app->scoped('invenbin', function (): InvenbinPanelManager {
            return new InvenbinPanelManager();
        });

    }
}