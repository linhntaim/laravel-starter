<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => 'Api',
], function () {
    #region Common
    Route::get('prerequisite', 'PrerequisiteController@index');

    Route::post('device/current', 'DeviceController@currentStore');

    Route::post('auth/login', 'LoginController@issueToken');
    Route::post('auth/password', 'PasswordController@store');
    Route::get('auth/password', 'PasswordController@show');

    Route::get('handled-file/{id}', 'HandledFileController@show')->name('handled_file.show');

    Route::group([
        'prefix' => 'role',
        'namespace' => 'Admin',
    ], function () {
        Route::get('/', 'RoleController@index');
    });
    #endregion

    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        Route::post('auth/logout', 'LogoutController@logout');

        Route::group([
            'prefix' => 'account',
            'namespace' => 'Account',
        ], function () {
            Route::get('/admin', 'AdminAccountController@index');
            Route::post('/admin', 'AdminAccountController@store');
        });
    });

    Route::group([
        'namespace' => 'Home',
    ], function () {
        // TODO: Home API
    });

    Route::group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
    ], function () {
        Route::post('register', 'RegisterController@store');

        Route::group([
            'middleware' => ['authenticated.passport.request', 'auth:api', 'authorized.admin'],
        ], function () {
            Route::group([
                'prefix' => 'command',
            ], function () {
                Route::get('/', 'CommandController@index')
                    ->middleware('authorized.permissions:be-owner');
                Route::post('/', 'CommandController@run')
                    ->middleware('authorized.permissions:be-owner');
            });

            Route::group([
                'prefix' => 'system-log',
            ], function () {
                Route::get('/', 'SystemLogController@index')
                    ->middleware('authorized.permissions:be-owner');
            });

            Route::group([
                'prefix' => 'app-option',
            ], function () {
                Route::post('/', 'AppOptionController@store');
            });

            Route::group([
                'prefix' => 'data-export',
            ], function () {
                Route::get('{id}', 'DataExportController@show');
            });

            Route::group([
                'prefix' => 'handled-file',
            ], function () {
                Route::post('/', 'HandledFileController@store');
                Route::post('{id}', 'HandledFileController@update');
                Route::post('ck-editor-simple-upload', 'HandledFileController@storeCkEditorSimpleUpload');
            });

            Route::group([
                'prefix' => 'role',
            ], function () {
                Route::get('/', 'RoleController@index')
                    ->middleware('authorized.permissions:role-manage');
                Route::post('/', 'RoleController@store')
                    ->middleware('authorized.permissions:role-manage');
                Route::get('{id}', 'RoleController@show')
                    ->middleware('authorized.permissions:role-manage');
                Route::post('{id}', 'RoleController@update')
                    ->middleware('authorized.permissions:role-manage');
            });
        });
    });
});
