<?php

namespace Tests;

use WpStarter\Foundation\Http\Kernel;

class HttpKernel extends Kernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \WpStarter\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \WpStarter\Foundation\Http\Middleware\ValidatePostSize::class,
        \WpStarter\Foundation\Http\Middleware\TrimStrings::class,
        \WpStarter\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \WpStarter\Cookie\Middleware\EncryptCookies::class,
            \WpStarter\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \WpStarter\Session\Middleware\StartSession::class,
            \WpStarter\View\Middleware\ShareErrorsFromSession::class,
            \WpStarter\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \WpStarter\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
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
        'auth' => \WpStarter\Auth\Middleware\Authenticate::class,
        'auth.basic' => \WpStarter\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \WpStarter\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \WpStarter\Http\Middleware\SetCacheHeaders::class,
        'can' => \WpStarter\Auth\Middleware\Authorize::class,
        'guest' => \Orchestra\Testbench\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \WpStarter\Routing\Middleware\ValidateSignature::class,
        'throttle' => \WpStarter\Routing\Middleware\ThrottleRequests::class,
        'verified' => \WpStarter\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \WpStarter\Session\Middleware\StartSession::class,
        \WpStarter\View\Middleware\ShareErrorsFromSession::class,
        \WpStarter\Auth\Middleware\Authenticate::class,
        \WpStarter\Session\Middleware\AuthenticateSession::class,
        \WpStarter\Routing\Middleware\SubstituteBindings::class,
        \WpStarter\Auth\Middleware\Authorize::class,
    ];
}
