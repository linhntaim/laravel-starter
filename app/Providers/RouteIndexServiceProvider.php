<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteIndexServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(function () {
                    Route::group([
                        // TODO:

                        // TODO
                    ], function () {
                        Route::match(['get', 'post', 'put', 'delete'], '/{path?}', [\App\Http\Controllers\Api\IndexController::class, 'index'])
                            ->where('path', '.*');
                    });
                });

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(function () {
                    Route::group([
                        // TODO:

                        // TODO
                    ], function () {
                        Route::match(['get', 'post', 'put', 'delete'], '/{path?}', [\App\Http\Controllers\Web\IndexController::class, 'index'])
                            ->where('path', '.*');
                    });
                });
        });
    }
}
