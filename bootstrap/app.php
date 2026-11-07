<?php

use App\Http\Middleware\IdleTimeoutMiddleware;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Client\ConnectionException as HttpConnectionException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Predis\Connection\ConnectionException as RedisConnectionException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        $exceptions->report(function (Throwable $exception): void {
            $isFatal = $exception instanceof QueryException
                || $exception instanceof HttpConnectionException
                || $exception instanceof RedisConnectionException
                || $exception instanceof RedisException;

            if (! $isFatal) {
                return;
            }

            Log::error('Terjadi kesalahan sistem.', [
                'url' => request()->fullUrl(),
                'user_id' => Auth::id(),
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);
        });

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

        $exceptions->render(function (HttpExceptionInterface $exception) {
            if ($exception->getStatusCode() < 500) {
                return null;
            }

            Log::error('Terjadi kesalahan sistem.', [
                'url' => request()->fullUrl(),
                'user_id' => Auth::id(),
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            return null;
        });
    })->create();
