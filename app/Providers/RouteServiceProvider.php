<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        Route::middleware('api')
            ->prefix('api')
            ->group(function () {
                $this->loadRoutesFrom(base_path('routes/api.php'));
                $this->loadRoutesFrom(base_path('routes/Users/UserRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/Auth/AuthRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/RolesAndPermissions/RolesAndPermissionRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/Clients/ClientRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/Cities/CityRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/DocumentTypes/DocumentTypeRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/Products/ProductRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/Invoices/InvoiceRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/Categories/CategoryRoutes.php'));
                $this->loadRoutesFrom(base_path('routes/Providers/ProviderRoutes.php'));
            });

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
