<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsStaff;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // បន្ថែមបន្ទាត់ខាងក្រោមនេះ ដើម្បីឱ្យ Inertia ស្គាល់ Flash និង Errors
        $middleware->web(append: [
            HandleInertiaRequests::class,
            EncryptCookies::class,
        ]);

        $middleware->alias([
            'is_admin' => IsAdmin::class,
            'is_staff' => IsStaff::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
