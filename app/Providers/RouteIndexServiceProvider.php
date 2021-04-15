<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Providers;

use App\Http\Controllers\Web\IndexController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteIndexServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(function () {
                    Route::group([
                        // TODO:

                        // TODO
                    ], function () {
                        Route::get('/{path?}', [IndexController::class, 'index'])
                            ->where('path', '^(?!api)|^(?!api\/).*');
                    });
                });
        });
    }
}
