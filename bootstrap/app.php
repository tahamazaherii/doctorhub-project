<?php

use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->renderable(function (NotFoundHttpException $exceptions) {
            return response()->json([
                'message' => 'Record not found.',
                'error' => $exceptions->getMessage()
            ], 404);
        });
        $exceptions->renderable(function (ModelNotFoundException $exceptions) {
            return response()->json([
                'message' => 'Record not found.',
                'error' => $exceptions->getMessage()
            ], 404);
        });
        $exceptions->renderable(function (QueryException $exceptions) {
            return response()->json([
                'message' => 'server error.',
                'error' => $exceptions->getMessage()
            ], 500);
        });
        $exceptions->renderable(function (MethodNotAllowedHttpException $exceptions) {
            return response()->json([
                'message' => 'server error.',
                'error' => $exceptions->getMessage()
            ], 500);
        });
        $exceptions->renderable(function (RelationNotFoundException $exceptions) {
            return response()->json([
                'message' => 'server error.',
                'error' => $exceptions->getMessage()
            ], 500);
        });

    })->create();
