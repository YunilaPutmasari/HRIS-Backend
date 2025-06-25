<!-- protected $routeMiddleware = [
    // ...
    'manager.admin' => \App\Http\Middleware\EnsureUserIsManagerAndAdmin::class,
    'employee.only' => \App\Http\Middleware\EnsureUserIsEmployee::class,
]; -->

protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

    // ✅ Custom middleware kamu
    'manager.admin' => \App\Http\Middleware\EnsureUserIsManagerAndAdmin::class,
    'employee.only' => \App\Http\Middleware\EnsureUserIsEmployee::class,
];
