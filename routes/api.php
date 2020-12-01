<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

use App\Http\Controllers\Api\Account\AccountController;
use App\Http\Controllers\Api\Account\AdminAccountController;
use App\Http\Controllers\Api\Account\AdminNotificationController;

// TODO: Import Account Controller

// TODO

use App\Http\Controllers\Api\Admin\Auth\RegisterController as AdminRegisterController;
use App\Http\Controllers\Api\Admin\Auth\PasswordController as AdminPasswordController;
use App\Http\Controllers\Api\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Api\Admin\CommandController as AdminCommandController;
use App\Http\Controllers\Api\Admin\SystemLogController as AdminSystemLogController;
use App\Http\Controllers\Api\Admin\AppOptionController as AdminAppOptionController;
use App\Http\Controllers\Api\Admin\DataExportController as AdminDataExportController;
use App\Http\Controllers\Api\Admin\HandledFileController as AdminHandledFileController;
use App\Http\Controllers\Api\Admin\RoleController as AdminRoleController;

// TODO: Import Admin Controller

// TODO

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;

// TODO: Import Home Controller

// TODO

use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\HandledFileController;
use App\Http\Controllers\Api\PrerequisiteController;

// TODO: Import Common Controller

// TODO

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
    // API
], function () {
    #region Common
    Route::get('prerequisite', [PrerequisiteController::class, 'index']);

    Route::post('device/current', [DeviceController::class, 'currentStore']);

    Route::get('handled-file/{id}', [HandledFileController::class, 'show'])->name('handled_file.show');
    #endregion

    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::post('login', [LoginController::class, 'issueToken']);
        Route::post('register', [RegisterController::class, 'store']);
    });

    Route::group([
        'middleware' => ['auth:api', 'impersonate'],
    ], function () {
        Route::group([
            'prefix' => 'auth',
        ], function () {
            Route::post('logout', [LogoutController::class, 'logout']);
        });

        Route::group([
            'prefix' => 'account',
        ], function () {
            Route::get('/', [AccountController::class, 'index']);
            Route::post('/', [AccountController::class, 'store']);

            Route::group([
                'prefix' => 'admin',
                'middleware' => ['admin', 'authorized.admin'],
            ], function () {
                Route::get('/', [AdminAccountController::class, 'index']);
                Route::post('/', [AdminAccountController::class, 'store']);

                Route::group([
                    'prefix' => 'notification',
                ], function () {
                    Route::get('/', [AdminNotificationController::class, 'index']);
                    Route::post('{id}', [AdminNotificationController::class, 'update']);
                });

                // TODO:

                // TODO
            });

            // TODO:

            // TODO
        });
    });

    Route::group([
        'prefix' => 'home',
    ], function () {
        // TODO: Home API

        // TODO
    });

    Route::group([
        'prefix' => 'admin',
        'middleware' => ['admin'],
    ], function () {
        // Anonymous
        Route::group([
            'prefix' => 'auth',
        ], function () {
            Route::post('register', [AdminRegisterController::class, 'store']);
            Route::post('password', [AdminPasswordController::class, 'store']);
            Route::get('password', [AdminPasswordController::class, 'index']);
        });

        // Authenticated
        Route::group([
            'middleware' => ['authenticated.passport.request', 'auth:api', 'authorized.admin', 'impersonate'],
        ], function () {
            Route::group([
                'middleware' => 'authorized.admin.permissions:be-super-admin',
            ], function () {
                Route::group([
                    'prefix' => 'command',
                ], function () {
                    Route::get('/', [AdminCommandController::class, 'index']);
                    Route::post('/', [AdminCommandController::class, 'run']);
                });

                Route::group([
                    'prefix' => 'system-log',
                ], function () {
                    Route::get('/', [AdminSystemLogController::class, 'index']);
                    Route::get('{id}', [AdminSystemLogController::class, 'show'])
                        ->where('id', '.+')
                        ->name('admin.system_log.show');
                });
            });

            Route::group([
                'prefix' => 'app-option',
            ], function () {
                Route::post('/', [AdminAppOptionController::class, 'store']);
            });

            Route::group([
                'prefix' => 'data-export',
            ], function () {
                Route::get('/', [AdminDataExportController::class, 'index']);
                Route::get('{id}', [AdminDataExportController::class, 'show']);
            });

            Route::group([
                'prefix' => 'handled-file',
            ], function () {
                Route::post('/', [AdminHandledFileController::class, 'store']);
                Route::post('ck-editor-simple-upload', [AdminHandledFileController::class, 'storeCkEditorSimpleUpload']);
                Route::post('{id}', [AdminHandledFileController::class, 'update']);
            });

            Route::group([
                'prefix' => 'activity-log',
            ], function () {
                Route::get('/', [AdminActivityLogController::class, 'index'])
                    ->middleware('authorized.admin.permissions:activity-log-manage');
                Route::get('{id}', [AdminActivityLogController::class, 'show'])
                    ->middleware('authorized.admin.permissions:activity-log-manage');
            });

            Route::group([
                'prefix' => 'role',
            ], function () {
                Route::get('/', [AdminRoleController::class, 'index'])
                    ->middleware('authorized.admin.permissions:role-manage');
                Route::post('/', [AdminRoleController::class, 'store'])
                    ->middleware('authorized.admin.permissions:role-manage');
                Route::get('{id}', [AdminRoleController::class, 'show'])
                    ->middleware('authorized.admin.permissions:role-manage');
                Route::post('{id}', [AdminRoleController::class, 'update'])
                    ->middleware('authorized.admin.permissions:role-manage');
            });

            // TODO: Expand Admin API

            // TODO
        });
    });
});
