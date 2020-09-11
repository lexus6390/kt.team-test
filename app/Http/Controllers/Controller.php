<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Выбрасывание ошибки 400 Bad Request
     *
     * @param $errors
     * @return JsonResponse|object
     */
    public static function badRequestException($errors) : JsonResponse
    {
        $arrayOfErrors = [];
        foreach ($errors as $field => $error) {
            $arrayOfErrors[] = [
                'status' => '400',
                'source' => ['parameter' => $field],
                'title'  => 'Bad request',
                'detail' => $error[0]
            ];
        }
        return response()
            ->json(['errors' => $arrayOfErrors])
            ->setStatusCode(400);
    }

    /**
     * Выбрасывание ошибки 404 Not Found
     *
     * @param string $pointer
     * @param string $model
     * @param int $id
     * @return JsonResponse|object
     */
    public static function notFoundException(string $pointer, string $model, int $id) : JsonResponse
    {
        return response()->json(['errors' => [
            [
                'status' => '404',
                'source' => ['pointer' => $pointer],
                'title'  => 'Not found',
                'detail' => "{$model} with ID {$id} not found"
            ]
        ]])->setStatusCode(404);
    }

    /**
     * Выбрасывание ошибки 500 Internal Server Error
     *
     * @param string $pointer
     * @param string $model
     * @return JsonResponse
     */
    public static function internalServerError(string $pointer, string $model) : JsonResponse
    {
        return response()
            ->json(['errors' => [
                [
                    'status' => '500',
                    'source' => ['pointer' => $pointer],
                    'title'  => 'Internal Server Error',
                    'detail' => "Ошибка при сохранении сущности {$model}"
                ]
            ]])
            ->setStatusCode(500);
    }
}
