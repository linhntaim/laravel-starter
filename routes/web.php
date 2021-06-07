<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

// TODO:

// TODO

use App\Vendors\Illuminate\Support\Facades\App;
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
    if (!App::runningInProduction() && class_exists('App\Http\Controllers\Web\TestWebController')) {
        Route::any('test', ['App\Http\Controllers\Web\TestWebController', 'test'])->name('debug.test');
    }

    // TODO:

    // TODO
});
