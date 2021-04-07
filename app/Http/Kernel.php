<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http;

use App\Http\Middleware\AuthenticatedByPassportViaCookie;
use App\Http\Middleware\AuthenticatedByPassportViaHeader;
use App\Http\Middleware\AuthenticatedByPassportViaRequest;
use App\Http\Middleware\AuthorizedWithAdmin;
use App\Http\Middleware\AuthorizedWithAdminPermissions;
use App\Http\Middleware\Device;
use App\Http\Middleware\HeaderDecrypt;
use App\Http\Middleware\Impersonate;
use App\Http\Middleware\IpLimitation;
use App\Http\Middleware\JapaneseTime;
use App\Http\Middleware\OverrideAuthorizationHeader;
use App\Http\Middleware\Screen;
use App\Http\Middleware\Settings;
use App\Http\Middleware\Web\Device as WebDevice;
use App\Http\Middleware\Web\Settings as WebSettings;
use App\Http\Middleware\Web\ViewShare;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        //\Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \App\Http\Middleware\CheckForClientLimitation::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            WebSettings::class,
            WebDevice::class,
            ViewShare::class,
        ],

        'api' => [
            'throttle:api',
            OverrideAuthorizationHeader::class,
            Settings::class,
            Screen::class,
            Device::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'client' => CheckClientCredentials::class,

        'japanese_time' => JapaneseTime::class,
        'header.decrypt' => HeaderDecrypt::class,
        'authenticated.passport.cookie' => AuthenticatedByPassportViaCookie::class,
        'authenticated.passport.header' => AuthenticatedByPassportViaHeader::class,
        'authenticated.passport.request' => AuthenticatedByPassportViaRequest::class,
        'authorized.admin' => AuthorizedWithAdmin::class,
        'authorized.admin.permissions' => AuthorizedWithAdminPermissions::class,
        'impersonate' => Impersonate::class,
        'ip.limit' => IpLimitation::class,

        // TODO:

        // TODO
    ];

    protected $middlewarePriority = [
        IpLimitation::class,

        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,

        JapaneseTime::class,
        OverrideAuthorizationHeader::class,
        HeaderDecrypt::class,
        Settings::class,
        WebSettings::class,
        Screen::class,
        Device::class,
        WebDevice::class,

        \Illuminate\Session\Middleware\AuthenticateSession::class,

        AuthenticatedByPassportViaCookie::class,
        AuthenticatedByPassportViaRequest::class,
        \App\Http\Middleware\Authenticate::class,
        Impersonate::class,

        \Illuminate\Routing\Middleware\SubstituteBindings::class,

        AuthorizedWithAdmin::class,
        AuthorizedWithAdminPermissions::class,

        \Illuminate\Auth\Middleware\Authorize::class,

        ViewShare::class,
    ];
}
