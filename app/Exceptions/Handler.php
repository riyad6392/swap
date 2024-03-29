<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $this->renderable(function (Throwable $exception, $request) {

//            if ($exception instanceof NotFoundHttpException && $request->is('api/*')) {
//                return response()->json(['success' => false, 'message' => 'Data not found'], 404);
//            }
//
//            if ($request->is('api/*')) {
//                return response()->json(['success' => false, 'message' => 'Failed to retrieve data'], 500);
//            }
        });
    }
}
