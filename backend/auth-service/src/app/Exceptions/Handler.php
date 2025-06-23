<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

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
        // Report exceptions
        $this->renderable(function (\Throwable $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        });
    }

    // Override render method for global control
    public function render($request, \Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->validator->errors(),
            ], 422);
        }

        if ($e instanceof QueryException) {
            return response()->json([
                'message' => 'Database query error',
                'errors' => $e->getMessage(),
            ], 500);
        }

        if ($e instanceof AuthException) {
            return response()->json([
                'message' => "Authentication error",
                'errors' => $e->getMessage(),
            ], $e->getCode());
        }

        return parent::render($request, $e);
    }
}
