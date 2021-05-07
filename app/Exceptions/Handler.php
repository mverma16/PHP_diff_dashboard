<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as ComponentNotFoundHttpException;
use \Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        ThrottleRequestsException::class,
        ComponentNotFoundHttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if(stripos($request->url(), 'api') || $request->ajax() || $request->wantsJson()){
            return $this->responseForApi($request, $exception);   
        }

        return parent::render($request, $exception);
    }

    public function responseForApi($request, Throwable $exception)
    {
        switch ($exception) {
            case $exception instanceof MethodNotAllowedHttpException:
                // Handle invalid HTTP method called
                return errorResponse("method not allowed", 405);

            case $exception instanceof NotFoundHttpException:
            case $exception instanceof ModelNotFoundException:
            case $exception instanceof ComponentNotFoundHttpException:
                // Handle invalid end point called
                return errorResponse("invalid API endpoint", 404);

            // case $exception instanceof ValidationException:
            //     return $this->invalidJson($request, $exception);

            case $exception instanceof ThrottleRequestsException:
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage()
                ], $exception->getStatusCode())->withHeaders($exception->getHeaders());

            default:
                // if debug mode is ON, actual exception message should go else internal-server-error
                return config("app.debug") ? exceptionResponse($exception) : errorResponse("internal server error", 500);
        }
    }
}
