<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

// TODO:

// TODO

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    // TODO:

    // TODO
], function () {
    #region Authentication
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::get('login', [\App\Http\Controllers\Web\Auth\LoginController::class, 'index'])
            ->middleware('guest')
            ->name('login');
        Route::post('login', [\App\Http\Controllers\Web\Auth\LoginController::class, 'store'])
            ->middleware('guest');

        // TODO:

        // TODO
    });
    #endregion

    #region Authenticated
    Route::group([
        'middleware' => ['auth'],
    ], function () {
        Route::group([
            'prefix' => 'auth',
        ], function () {
            Route::post('logout', [\App\Http\Controllers\Web\Auth\LogoutController::class, 'logout'])
                ->name('logout');

            // TODO:

            // TODO
        });

        // Account
        Route::group([
            'prefix' => 'account',
        ], function () {
            Route::get('/', [HomeAccountController::class, 'index']);
            Route::post('/', [HomeAccountController::class, 'store']);

            // TODO:

            // TODO
        });

        // TODO:

        // TODO
    });
    #endregion

    // TODO:

    // TODO
});
