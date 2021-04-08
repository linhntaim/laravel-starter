<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

// TODO:

// TODO

use App\Http\Controllers\Web\Home\Auth\LoginController as HomeLoginController;
use App\Http\Controllers\Web\Home\Auth\LogoutController as HomeLogoutController;

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
    'middleware' => ['custom_timezone'],
    // TODO
    // TODO:

    // TODO
], function () {
    #region Authentication
    Route::group([
        'prefix' => 'auth',
        'middleware' => ['guest'],
    ], function () {
        Route::get('login', [HomeLoginController::class, 'index'])
            ->name('login');
        Route::post('login', [HomeLoginController::class, 'store']);

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
            Route::match(['get', 'post'], 'logout', [HomeLogoutController::class, 'logout'])
                ->name('logout');

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
