<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                $httpStatus = 401;
                $res['message'] = 'Silahkan gunakan Token yang Valid pada Authorization type Bearer Token';
                $res['status'] = 'Unauthorized';
                $res['http_status'] = $httpStatus;
                return response()->json($res, $httpStatus);
            }
        });
    }

    public function report(Throwable $exception)
    {
        if (app()->environment('production') && $this->shouldReport($exception)) {
            if (app()->bound('sentry') && $this->shouldReport($exception)) {
                app('sentry')->captureException($exception);
            }

            parent::report($exception);
        }
    }
}
