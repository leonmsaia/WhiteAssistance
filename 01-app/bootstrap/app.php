<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Modules\AccessControl\Domain\RoleName;
use App\Support\DashboardResolver;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'active' => EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            $user = $request->user();

            if (! $user) {
                return null;
            }

            if (! $user->hasAnyRole([
                RoleName::ADMIN,
                RoleName::SPECIALIST,
                RoleName::PATIENT,
            ])) {
                return null;
            }

            return redirect()->route(
                DashboardResolver::routeName($user)
            );
        });
    })->create();
