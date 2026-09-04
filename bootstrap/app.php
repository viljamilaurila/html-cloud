<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The site is stateless: no accounts, no forms, no flash data, and the
        // API is authorized by edit keys rather than sessions. Without the
        // session stack no cookie is ever set and no session row is written.
        $middleware->web(
            remove: [
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
            ],
            append: [SecurityHeaders::class],
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // A missing document and an expired one look the same to visitors:
        // route binding refuses both (see Document::resolveRouteBinding).
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $e->getPrevious() instanceof ModelNotFoundException) {
                return null;
            }

            return $request->is('api/*')
                ? response()->json(['error' => 'Not found or expired'], 404)
                : response()->view('expired', [], 404);
        });
    })->create();
