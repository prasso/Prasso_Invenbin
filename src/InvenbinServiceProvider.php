<?php

namespace Faxt\Invenbin;

use Illuminate\Support\ServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Facades\Filament;
use Faxt\Invenbin\Filament\Resources\ErpProductResource;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages;
use Faxt\Invenbin\Support\Facades\InvenbinPanel;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers\CategoriesRelationManager;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers\ImagesRelationManager;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers\ProductDescriptorsRelationManager;



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
        Livewire::component('faxt.invenbin.filament.resources.erp-product-resource.relation-managers.categories-relation-manager', CategoriesRelationManager::class);
        Livewire::component('faxt.invenbin.filament.resources.erp-product-resource.relation-managers.images-relation-relation-manager', ImagesRelationManager::class);
        Livewire::component('faxt.invenbin.filament.resources.erp-product-resource.relation-managers.product-descriptors-relation-manager', ProductDescriptorsRelationManager::class);

        Livewire::component('faxt.invenbin.filament.resources.erp-product-resource.pages.edit-erp-product', \Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages\EditErpProduct::class);
        Livewire::component('faxt.invenbin.filament.resources.erp-bill-of-material-resource.pages.edit-erp-bill-of-material', \Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages\EditErpBillOfMaterial::class);
        Livewire::component('faxt.invenbin.filament.resources.erp-category-resource.pages.edit-erp-category', \Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages\EditErpCategory::class);
        Livewire::component('faxt.invenbin.filament.resources.erp-product-status-resource.pages.edit-erp-product-status', \Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\Pages\EditErpProductStatus::class);
        Livewire::component('faxt.invenbin.filament.resources.erp-product-type-resource.pages.edit-erp-product-type', \Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages\EditErpProductType::class);
        Livewire::component('faxt.invenbin.filament.resources.erp-product-usage-log-resource.pages.edit-erp-product-usage-log', \Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\Pages\EditErpProductUsageLog::class);
        Livewire::component('faxt.invenbin.filament.resources.erp-unit-of-measure-resource.pages.edit-erp-unit-of-measure', \Faxt\Invenbin\Filament\Resources\ErpUnitOfMeasureResource\Pages\EditErpUnitOfMeasure::class);

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