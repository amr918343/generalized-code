<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use http\Exception\UnexpectedValueException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use function Psy\debug;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Whoops\Exception\ErrorException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {


        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
//
        if($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse('The ' . $model . ' with number: ' . $exception->getIds()[0] . ' is not found!', 404);

        }
//
        if ($exception instanceof AuthenticationException)
            return $this->errorResponse('Not Authenticated.', 401);
//
        if ($exception instanceof AuthorizationException)
            return $this->errorResponse('Not Authorized.', 403);
//
        if ($exception instanceOf NotFoundHttpException)
            return $this->errorResponse('URL is wrong. This page is not found!', 404);
//
        if($exception instanceof MethodNotAllowedHttpException)
            return $this->errorResponse($exception->getMessage(), 405);
//
        if ($exception instanceof HttpException)
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
//
        if ($exception instanceof QueryException)
        {
                return $this->errorResponse('Cannot remove this resource permanently, it is related with any other resource', 409);
        }

        if (config('app.debug'))
            return parent::render($request, $exception);

        return $this->errorResponse('Unexpected Exception, come again later', 500);


    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        return $this->errorResponse($errors, 422);
    }
}
