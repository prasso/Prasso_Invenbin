<?php

namespace Faxt\Invenbin;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Table;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Faxt\Invenbin\Filament\AvatarProviders\GravatarProvider;
use Faxt\Invenbin\Filament\Pages;
use Faxt\Invenbin\Filament\Resources;
use Illuminate\Support\Facades\Log;

class InvenbinPanelManager
{
    protected ?\Closure $closure = null;

    protected array $extensions = [];

    protected string $panelId = 'invenbin';

    protected static $invenbin_resources = [
        Resources\ErpBillOfMaterialResource::class,
        Resources\ErpCategoryResource::class,
        Resources\ErpProductResource::class,
        Resources\ErpProductStatusResource::class,
        Resources\ErpProductTypeResource::class,
        Resources\ErpProductUsageLogResource::class,
        Resources\ErpUnitOfMeasureResource::class,

    ];

    protected static $invenbin_pages = [
    ];

    protected static $invenbin_widgets = [
        
    ];

    public function register(): self
    {
        
        $panel = $this->defaultPanel();
        
        if ($this->closure instanceof \Closure) {
            $fn = $this->closure;
            $panel = $fn($panel);
        }

        Filament::registerPanel($panel);

        FilamentColor::register([
            'chartPrimary' => Color::Blue,
            'chartSecondary' => Color::Green,
        ]);

        if (app('request')->is($panel->getPath().'*')) {
            app('config')->set('livewire.inject_assets', true);
        }

        Table::configureUsing(function (Table $table): void {
            $table
                ->paginationPageOptions([10, 25, 50, 100])
                ->defaultPaginationPageOption(25);
        });

        return $this;
    }

    public function panel(\Closure $closure): self
    {
        $this->closure = $closure;

        return $this;
    }

    public function getPanel(): Panel
    {
        return Filament::getPanel($this->panelId);
    }

    protected function defaultPanel(): Panel
    {

        $panelMiddleware = [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];

        return Panel::make()
            //->spa()
            //->default()
           ->id($this->panelId)
           ->brandName('Faxt Invenbin')
            ->path('Faxt\Invenbin')
           // ->authGuard('staff')
           // ->login()
            ->colors([
                'primary' => Color::Sky,
            ])
            ->font('Poppins')
            ->middleware($panelMiddleware)
            ->pages(
                static::getInvenbinPages()
            )
            ->resources(
                static::getInvenbinResources()
            )
            ->widgets(
                static::getInvenbinWidgets()
            )
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
               
            ]);
    }

    public function extensions(array $extensions): self
    {
        foreach ($extensions as $class => $extension) {
            $this->extensions[$class][] = new $extension;
        }

        return $this;
    }

    public static function getInvenbinResources()
    {
        return static::$invenbin_resources;
    }

    public static function getInvenbinPages()
    {
        return static::$invenbin_pages;
    }

    /**
     * @return string[]
     */
    public static function getInvenbinWidgets(): array
    {
        return static::$invenbin_widgets;
    }

    public function callHook(string $class, string $hookName, ...$args): mixed
    {
        if (isset($this->extensions[$class])) {
            foreach ($this->extensions[$class] as $extension) {
                if (method_exists($extension, $hookName)) {
                    $args[0] = $extension->{$hookName}(...$args);
                }
            }
        }

        return $args[0];
    }
}
