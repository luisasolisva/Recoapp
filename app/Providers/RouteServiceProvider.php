<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // Patrones globales para IDs UUID
        Route::pattern('recorrido', '[0-9a-fA-F\-]{36}');
        Route::pattern('vehiculo', '[0-9a-fA-F\-]{36}');
        Route::pattern('ruta', '[0-9a-fA-F\-]{36}');

        // Registrar rutas API y Web
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // 🔹 Definir rate limiter "api" para evitar el error MissingRateLimiterException
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
