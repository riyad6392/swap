<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        /*$this->renderable(function (Throwable $exception, $request) {

            if ($exception instanceof NotFoundHttpException && $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Data not found'], 404);
            }

            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Failed to retrieve data'], 500);
            }
        });*/
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $message = config('app.debug') === true ? $exception->getMessage() : 'Data not found';
            return apiResponseWithError($message, Response::HTTP_NOT_FOUND);
        } elseif ($exception instanceof ValidationException) {
            return apiResponseWithError($exception, Response::HTTP_UNPROCESSABLE_ENTITY);
        } elseif ($exception instanceof AuthenticationException) {
            $message = config('app.debug') === true ? $exception->getMessage() : 'Unauthenticated';
            return apiResponseWithError($message, Response::HTTP_UNAUTHORIZED);
        } elseif ($exception instanceof  UnauthorizedException) {
            $message = config('app.debug') === true ? $exception->getMessage() : 'Unauthorized';
            return apiResponseWithError($message, Response::HTTP_UNAUTHORIZED);
        } else {
            $message = config('app.debug') === true ? $exception->getMessage() : 'Something went wrong';
            return apiResponseWithError($message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
