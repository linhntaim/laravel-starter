<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

use App\Http\Controllers\Api\HandledFileController;

// Common common
use App\Http\Controllers\Api\Common\DeviceController as CommonDeviceController;
use App\Http\Controllers\Api\Common\PrerequisiteController as CommonPrerequisiteController;

// Common auth
use App\Http\Controllers\Api\Common\Auth\LoginController as CommonLoginController;
use App\Http\Controllers\Api\Common\Auth\LogoutController as CommonLogoutController;

// Common account
use App\Http\Controllers\Api\Common\Account\AccountController as CommonAccountController;

// Admin common
use App\Http\Controllers\Api\Admin\DeviceController as AdminDeviceController;
use App\Http\Controllers\Api\Admin\HandledFileController as AdminHandledFileController;
use App\Http\Controllers\Api\Admin\PrerequisiteController as AdminPrerequisiteController;

// Admin auth
use App\Http\Controllers\Api\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Api\Admin\Auth\LogoutController as AdminLogoutController;
use App\Http\Controllers\Api\Admin\Auth\RegisterController as AdminRegisterController;
use App\Http\Controllers\Api\Admin\Auth\PasswordController as AdminPasswordController;
use App\Http\Controllers\Api\Admin\Auth\VerificationController as AdminVerificationController;

// Admin account
use App\Http\Controllers\Api\Admin\Account\AccountController as AdminAccountController;
use App\Http\Controllers\Api\Admin\Account\NotificationController as AdminAccountNotificationController;

// Admin manage
use App\Http\Controllers\Api\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Api\Admin\CommandController as AdminCommandController;
use App\Http\Controllers\Api\Admin\SystemLogController as AdminSystemLogController;
use App\Http\Controllers\Api\Admin\AppOptionController as AdminAppOptionController;
use App\Http\Controllers\Api\Admin\DataExportController as AdminDataExportController;
use App\Http\Controllers\Api\Admin\RoleController as AdminRoleController;

// TODO: Import Admin Controller

// TODO

// Home common
use App\Http\Controllers\Api\Home\DeviceController as HomeDeviceController;
use App\Http\Controllers\Api\Home\HandledFileController as HomeHandledFileController;
use App\Http\Controllers\Api\Home\PrerequisiteController as HomePrerequisiteController;

// Home auth
use App\Http\Controllers\Api\Home\Auth\LoginController as HomeLoginController;
use App\Http\Controllers\Api\Home\Auth\LogoutController as HomeLogoutController;
use App\Http\Controllers\Api\Home\Auth\RegisterController as HomeRegisterController;

// Home account
use App\Http\Controllers\Api\Home\Account\AccountController as HomeAccountController;

// TODO: Import Home Controller

// TODO

use App\Vendors\Illuminate\Support\Facades\App;
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
    // TODO:

    // TODO
], function () {
    if (!App::runningInProduction() && class_exists('App\Http\Controllers\Api\TestApiController')) {
        Route::any('test', ['App\Http\Controllers\Api\TestApiController', 'test'])->name('debug.test');
    }

    Route::get('handled-file/{id}', [HandledFileController::class, 'show'])->name('handled_file.show');

    // TODO:

    // TODO

    Route::group([
        'prefix' => 'account',
        'middleware' => ['authenticated.passport.cookie', 'authenticated.passport.request', 'auth:api'],
    ], function () {
        Route::get('handled-file/{id}', [HandledFileController::class, 'show'])->name('account.handled_file.show');
    });

    #region Home
    Route::group([
        'prefix' => 'home',
    ], function () {
        #region Common
        Route::get('prerequisite', [HomePrerequisiteController::class, 'index']);

        Route::post('device/current', [HomeDeviceController::class, 'currentStore']);

        // TODO:

        // TODO
        #endregion

        #region Authentication
        Route::group([
            'prefix' => 'auth',
        ], function () {
            Route::post('login', [HomeLoginController::class, 'issueToken']);
            Route::post('register', [HomeRegisterController::class, 'store']);

            // TODO:

            // TODO
        });
        #endregion

        #region Authenticated
        Route::group([
            'middleware' => ['auth:api', 'impersonate'],
        ], function () {
            Route::group([
                'prefix' => 'auth',
            ], function () {
                Route::post('logout', [HomeLogoutController::class, 'logout']);

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

            Route::group([
                'prefix' => 'handled-file',
            ], function () {
                Route::post('/', [HomeHandledFileController::class, 'store']);
                // TODO:

                // TODO
            });

            // TODO:

            // TODO
        });
        #endregion
    });
    #endregion

    #region Admin
    Route::group([
        'prefix' => 'admin',
    ], function () {
        #region Common
        Route::get('prerequisite', [AdminPrerequisiteController::class, 'index']);

        Route::post('device/current', [AdminDeviceController::class, 'currentStore']);

        // TODO:

        // TODO
        #endregion

        #region Authentication
        Route::group([
            'prefix' => 'auth',
        ], function () {
            Route::post('login', [AdminLoginController::class, 'issueToken']);
            Route::post('register', [AdminRegisterController::class, 'store']);
            Route::post('password', [AdminPasswordController::class, 'store']);
            Route::get('password', [AdminPasswordController::class, 'index']);
            Route::post('verify', [AdminVerificationController::class, 'store']);

            // TODO:

            // TODO
        });
        #endregion

        #region Authenticated
        Route::group([
            'middleware' => ['auth:api', 'authorized.admin', 'impersonate'],
        ], function () {
            Route::group([
                'prefix' => 'auth',
            ], function () {
                Route::post('logout', [AdminLogoutController::class, 'logout']);
            });

            // Account
            Route::group([
                'prefix' => 'account',
            ], function () {
                Route::get('/', [AdminAccountController::class, 'index']);
                Route::post('/', [AdminAccountController::class, 'store']);

                Route::group([
                    'prefix' => 'notification',
                ], function () {
                    Route::get('/', [AdminAccountNotificationController::class, 'index']);
                    Route::post('{id}', [AdminAccountNotificationController::class, 'update']);
                });

                // TODO:

                // TODO
            });

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
                        ->middleware(['authenticated.passport.cookie', 'authenticated.passport.request'])
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
                Route::get('{id}', [AdminDataExportController::class, 'show'])
                    ->middleware(['authenticated.passport.cookie', 'authenticated.passport.request']);
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

            // TODO:

            // TODO
        });
        #endregion
    });
    #endregion

    #region Common
    Route::group([
        'prefix' => 'common',
    ], function () {
        #region Common
        Route::get('prerequisite', [CommonPrerequisiteController::class, 'index']);
        Route::post('device/current', [CommonDeviceController::class, 'currentStore']);
        #endregion

        #region Authentication
        Route::group([
            'prefix' => 'auth',
        ], function () {
            Route::post('login', [CommonLoginController::class, 'issueToken']);
        });
        #endregion

        #region Authenticated
        Route::group([
            'middleware' => ['auth:api', 'impersonate'],
        ], function () {
            Route::group([
                'prefix' => 'auth',
            ], function () {
                Route::post('logout', [CommonLogoutController::class, 'logout']);
            });

            // Account
            Route::group([
                'prefix' => 'account',
            ], function () {
                Route::get('/', [CommonAccountController::class, 'index']);
                Route::post('/', [CommonAccountController::class, 'store']);
            });
        });
        #endregion
    });
    #endregion
});
