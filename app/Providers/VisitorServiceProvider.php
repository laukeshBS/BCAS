<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Visitor;
use App\Models\Admin\AuditTrail;

class VisitorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app['router']->pushMiddlewareToGroup('web', \App\Http\Middleware\TrackVisitors::class);

        View::share('visitorCount', Visitor::count());
        //View::share('last_updated', AuditTrail::count());
    }
}
