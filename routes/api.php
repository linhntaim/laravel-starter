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

    Route::get('handled-file/{id}', 'HandledFileController@show')->name('handled_file.show');
    #endregion

    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::post('login', 'LoginController@issueToken');
    });

    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        Route::group([
            'prefix' => 'auth',
            'namespace' => 'Auth',
        ], function () {
            Route::post('logout', 'LogoutController@logout');
        });

        Route::group([
            'prefix' => 'account',
            'namespace' => 'Account',
        ], function () {
            Route::group([
                'prefix' => 'admin',
            ], function () {
                Route::get('/', 'AdminAccountController@index');
                Route::post('/', 'AdminAccountController@store');
            });
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
        // Anonymous
        Route::group([
            'prefix' => 'auth',
            'namespace' => 'Auth',
        ], function () {
            Route::post('register', 'RegisterController@store');
            Route::post('password', 'PasswordController@store');
            Route::get('password', 'PasswordController@index');
        });

        // Authenticated
        Route::group([
            'middleware' => ['authenticated.passport.request', 'auth:api', 'authorized.admin'],
        ], function () {
            Route::group([
                'middleware' => 'authorized.permissions:be-super-admin',
            ], function () {
                Route::group([
                    'prefix' => 'command',
                ], function () {
                    Route::get('/', 'CommandController@index');
                    Route::post('/', 'CommandController@run');
                });

                Route::group([
                    'prefix' => 'system-log',
                ], function () {
                    Route::get('/', 'SystemLogController@index');
                    Route::get('{id}', 'SystemLogController@show')
                        ->where('id', '.+')
                        ->name('admin.system_log.show');
                });
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

            // TODO: Expand Admin API
        });
    });
});
