<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http;

use App\Http\Middleware\Api\Client as ClientApi;
use App\Http\Middleware\Api\ClientAuthorizationHeader;
use App\Http\Middleware\Api\Device as DeviceApi;
use App\Http\Middleware\Api\Screen as ScreenApi;
use App\Http\Middleware\Api\Settings as SettingsApi;
use App\Http\Middleware\AuthenticatedByPassportViaCookie;
use App\Http\Middleware\AuthenticatedByPassportViaHeader;
use App\Http\Middleware\AuthenticatedByPassportViaRequest;
use App\Http\Middleware\AuthorizedWithAdmin;
use App\Http\Middleware\AuthorizedWithAdminPermissions;
use App\Http\Middleware\CustomTimezone;
use App\Http\Middleware\Impersonate;
use App\Http\Middleware\IpLimitation;
use App\Http\Middleware\JapaneseTime;
use App\Http\Middleware\Web\Client as ClientWeb;
use App\Http\Middleware\Web\Device as DeviceWeb;
use App\Http\Middleware\Web\Locale;
use App\Http\Middleware\Web\Settings as SettingsWeb;
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
            ClientWeb::class,
            SettingsWeb::class,
            DeviceWeb::class,
            Locale::class,
            ViewShare::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ClientAuthorizationHeader::class,
            ClientApi::class,
            SettingsApi::class,
            DeviceApi::class,
            ScreenApi::class,
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

        'ip.limit' => IpLimitation::class,
        'custom_timezone' => CustomTimezone::class,
        'japanese_time' => JapaneseTime::class,
        'authenticated.passport.cookie' => AuthenticatedByPassportViaCookie::class,
        'authenticated.passport.header' => AuthenticatedByPassportViaHeader::class,
        'authenticated.passport.request' => AuthenticatedByPassportViaRequest::class,
        'authorized.admin' => AuthorizedWithAdmin::class,
        'authorized.admin.permissions' => AuthorizedWithAdminPermissions::class,
        'impersonate' => Impersonate::class,

        // TODO:

        // TODO
    ];

    protected $middlewarePriority = [
        IpLimitation::class,

        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,

        ClientAuthorizationHeader::class,
        ClientApi::class,
        ClientWeb::class,
        SettingsApi::class,
        SettingsWeb::class,
        CustomTimezone::class,
        JapaneseTime::class,
        DeviceApi::class,
        DeviceWeb::class,
        ScreenApi::class,

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
