<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        parent::boot();

        Route::bind('medo_city', function ($value, $route) {
            $province = $route->parameter('medo_province');
            if (!$province) {
                return \App\Models\Medo\City::where('slug', $value)->firstOrFail();
            }
            return \App\Models\Medo\City::where('slug', $value)
                ->where('province_id', $province->id)
                ->firstOrFail();
        });

        Route::bind('medo_job', function ($value, $route) {
            $city = $route->parameter('medo_city');
            if (!$city) {
                return \App\Models\Medo\Job::where('slug', $value)->firstOrFail();
            }
            return \App\Models\Medo\Job::where('slug', $value)
                ->where('city_id', $city->id)
                ->firstOrFail();
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();

        $this->mapAdminRoutes();
        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        $adminPrefix = config('app.admin_prefix', 'admin');

        Route::prefix($adminPrefix)
                ->middleware(['web', 'admin', 'auth:admin', 'checkAdminRoles'])
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
    }

}
