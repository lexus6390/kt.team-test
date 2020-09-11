<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
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
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception) : Response
    {
        if ($this->isHttpException($exception)) {
            /** @var HttpExceptionInterface $exception */

            return new Response(json_encode(['errors' => [
                [
                    'status' => (string)$exception->getStatusCode(),
                    'title'  => 'Resource not found',
                    'detail' => 'Nothing was found at the specified URL'
                ]
            ]]), $exception->getStatusCode(), ['Content-Type' => 'application/json']);
        }

        return parent::render($request, $exception);
    }
}
