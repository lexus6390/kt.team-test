<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Tobscure\JsonApi\Document;
use App\Services\UserSerializer;
use Tobscure\JsonApi\Parameters;
use Tobscure\JsonApi\Resource;

/**
 * Class UserController
 * Контроллер для работы с сущностью пользователя (User)
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Регистрация роутов для работы с сущностью пользователя (User)
     */
    public static function routesUser() : void
    {
        // Один пользователь
        Route::get('/users/{id}', [UserController::class, 'getUser']);

        // Добавление пользователя
        Route::post('/users', [UserController::class, 'addUser']);

        // Редактирование пользователя
        Route::patch('/users/{id}', [UserController::class, 'updateUser']);

        // Удаление пользователя
        Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
    }

    /**
     * Получение пользователя по ID
     *
     * @param $id int
     * @return \Illuminate\Http\JsonResponse
     * @throws \Tobscure\JsonApi\Exception\InvalidParameterException
     */
    public function getUser(int $id) : JsonResponse
    {
        $parameters = new Parameters($_GET);

        $fields = $parameters->getFields();
        $include = $parameters->getInclude(['tasks', 'roles']);

        $user = User::where('id', $id)->first();

        if(is_null($user)) {
            return response()->json(['errors' => [
                [
                    'status' => '404',
                    'source' => ['pointer' => 'users'],
                    'title'  => 'Not found',
                    'detail' => 'User with ID '.$id.' not found'
                ]
            ]])->setStatusCode(404);
        }

        $resource = (new Resource($user, new UserSerializer()))->fields($fields);

        $resource = UserHelper::addRelationshipResource($resource, $fields, $include);

        $document = new Document($resource);
        $document->addLink('self', 'https://example.com/api/users/'.$id);

        return response()->json($document);
    }

    /**
     * Создание нового пользователя
     *
     * @param Request $request
     * @return JsonResponse|object
     */
    public function addUser(Request $request)
    {
        $errors = User::createValidator($request->all())->errors()->getMessages();

        if(!empty($errors)) {
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

        $newUser = User::createUser($request);

        $resource = (new Resource($newUser, new UserSerializer()));

        $document = new Document($resource);
        $document->addLink('self', 'https://example.com/api/users/'.$newUser->id);

        return response()->json($document)
            ->setStatusCode(201)
            ->header('Location', 'http://ktteam-domain/api/users/'.$newUser->id);
    }

    public function updateUser(Request $request, int $id)
    {
        $errors = User::updateValidator($request->all())->errors()->getMessages();

        if(!empty($errors)) {
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

        $updateUser = User::updateUser($request, $id);

        $resource = (new Resource($updateUser, new UserSerializer()));

        $document = new Document($resource);
        $document->addLink('self', 'https://example.com/api/users/'.$updateUser->id);

        return response()->json($document)
            ->setStatusCode(201)
            ->header('Location', 'http://ktteam-domain/api/users/'.$updateUser->id);
    }

    /**
     * Удаление пользователя по ID
     *
     * @param $id int
     * @return \Illuminate\Http\JsonResponse|Response|object
     */
    public function deleteUser(int $id) : object
    {
        $deleteUser = User::destroy($id);

        if($deleteUser == 0) {
            return response()->json(['errors' => [
                [
                    'status' => '404',
                    'source' => ['pointer' => 'users'],
                    'title'  => 'Not found',
                    'detail' => 'T'
                ]
            ]])->setStatusCode(404);
        }

        return (new Response())->setStatusCode(204);
    }
}
