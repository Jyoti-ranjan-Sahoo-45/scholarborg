<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

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
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\SecurityHeaders::class,
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
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\UserLocale::class,
            \App\Http\Middleware\SessionValidity::class,
            \App\Http\Middleware\DebugBar::class,
            \App\Http\Middleware\EdgeCachePrimer::class,
            \App\Http\Middleware\LicenseCheck::class,
            \App\Http\Middleware\TrackUserActivity::class,
        ],

        'api' => [
            'throttle:300,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Api\CheckApiKey::class,
            \App\Http\Middleware\Api\SetLocale::class,
            \App\Http\Middleware\Api\CheckMaintenance::class,
            \App\Http\Middleware\Api\CheckRestrictionAPI::class,
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
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\AdminAuthenticate::class,
        'panel' => \App\Http\Middleware\PanelAuthenticate::class,
        'user.not.access' => \App\Http\Middleware\UserNotAccess::class,
        'web.auth' => \App\Http\Middleware\WebAuthenticate::class,
        'impersonate' => \App\Http\Middleware\Impersonate::class,
        'share' => \App\Http\Middleware\Share::class,
        'admin_locale' => \App\Http\Middleware\AdminLocale::class,
        'check_mobile_app' => \App\Http\Middleware\CheckMobileApp::class,
        'check_maintenance' => \App\Http\Middleware\CheckMaintenance::class,
        'check_restriction' => \App\Http\Middleware\CheckRestriction::class,
        'check_event_feature_status' => \App\Http\Middleware\CheckEventFeatureStatus::class,
        'check_jobs_feature_status' => \App\Http\Middleware\CheckJobsFeatureStatus::class,
        'api.auth' => \App\Http\Middleware\Api\Authenticate::class,
        'api.guest' => \App\Http\Middleware\Api\RedirectIfAuthenticated::class,
        'api.request.type' => \App\Http\Middleware\Api\RequestType::class,
        'api.identify' => \App\Http\Middleware\Api\CheckApiKey::class,
        'api.level-access' => \App\Http\Middleware\Api\LevelAccess::class,
        'check_restriction_api' => \App\Http\Middleware\Api\CheckRestrictionAPI::class,
        'x_frame_headers' => \App\Http\Middleware\Api\XFrameHeadersMiddleware::class,
        'mw9154726308' => \App\Http\Middleware\PluginBundleLicenseCheck::class,
        'mw2745619038' => \App\Http\Middleware\ThemeBuilderLicenseCheck::class,
        'mw6384012957' => \App\Http\Middleware\MobileAppLicenseCheck::class,
    ];
}
