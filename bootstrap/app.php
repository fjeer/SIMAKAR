<?php

use App\Http\Middleware\IdleTimeoutMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            IdleTimeoutMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (ThrottleRequestsException $exception, Request $request) {
            $message = 'Terlalu banyak percobaan login. Silakan coba lagi dalam satu menit.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], $exception->getStatusCode());
            }

            return back()
                ->withErrors(['nim_nidn' => $message])
                ->withInput();
        });
    })->create();
