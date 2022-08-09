<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {

        # Unauthorized - 401
        $this->renderable(function (RouteNotFoundException $exception) {
            return response()->errorMessage($exception->getMessage(), 401);
        });


        # Forbidden - 403
        $this->renderable(function (AuthorizationException $exception) {
            return response()->errorMessage($exception->getMessage(), 403);
        });

        $this->renderable(function (AccessDeniedHttpException $exception) {
            return response()->errorMessage($exception->getMessage(), 403);
        });


        # Not Found - 404
        $this->renderable(function (ModelNotFoundException $exception) {
            return response()->errorMessage($exception->getMessage(), 404);
        });

        $this->renderable(function (NotFoundHttpException $exception) {
            return response()->errorMessage($exception->getMessage(), 404);
        });

        $this->renderable(function (MethodNotAllowedHttpException $exception) {
            return response()->errorMessage($exception->getMessage(), 404);
        });


        # Data validation - 422
        $this->renderable(function (ValidationException $exception) {
            return response()->validationError($exception->getMessage(), $exception->errors(), 422);
        });


        # Bad Request - 400
        $this->renderable(function (\Exception $exception) {
            Log::error($exception);
            return response()->errorMessage($exception->getMessage(), 400);
        });


        $this->reportable(function (Throwable $e) {
            //
        });
    }

}
