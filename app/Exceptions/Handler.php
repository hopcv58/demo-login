<?php

namespace App\Exceptions;

use App\Components\Utilities\Responder;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     * @var array
     */
    protected $dontReport = [
        \League\OAuth2\Server\Exception\OAuthServerException::class,
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof HttpResponseException) {
            return $exception->getResponse();
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        // Method Not Allowed
        if ($exception instanceof NotFoundHttpException) {
            return Responder::notFound();
        }

        // Method Not Allowed
        if ($exception instanceof MethodNotAllowedHttpException) {
            return Responder::methodFalse();
        }

        if ($exception instanceof FatalErrorException) {
            // Request Timeout.
            if (strpos($message = $exception->getMessage(),'Maximum execution time') === 0) {
                return Responder::timeout($message);
            }
        }

        // Method Not Allowed
        if ($exception instanceof MaintenanceModeException) {
            return Responder::unavailable($exception->getMessage());
        }

        // Method Not Allowed
        if (! config('app.debug') and $exception instanceof Exception) {
            return Responder::error($exception->getMessage());
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return Responder::unauthorized($exception->getMessage());
    }

    /**
     * Create a response object from the given validation exception.
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $exception, $request)
    {
        if ($exception->response) {
            return $exception->response;
        }

        if ($exception->validator instanceof Validator) {
            $errors = $exception->validator->errors()->getMessages();

            if ($request->expectsJson()) {
                return response()->json($errors, 422);
            }
        }

        if (is_array($exception->validator)) {
            return Responder::invalid($exception->validator, trans('app.messages.invalidate'));
        }

        if (is_string($exception->validator)) {
            return Responder::invalid([], $exception->validator);
        }
    }
}
