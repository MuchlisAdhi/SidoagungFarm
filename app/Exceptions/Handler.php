<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if (! $request->expectsJson()) {
            if ($e instanceof UnauthorizedException) {
                return response()->view('errors.403', [
                    'message' => 'User does not have the right permissions.',
                ], 403);
            }

            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 403) {
                $message = trim((string) $e->getMessage());

                return response()->view('errors.403', [
                    'message' => $message !== '' ? $message : 'Anda tidak memiliki izin untuk mengakses halaman ini.',
                ], 403);
            }
        }

        return parent::render($request, $e);
    }
}
