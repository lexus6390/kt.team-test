<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tobscure\JsonApi\Document;
use App\Services\UserSerializer;
use Tobscure\JsonApi\Exception\InvalidParameterException;
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
     * @param $id int Идентификатор пользователя
     * @return JsonResponse
     * @throws InvalidParameterException
     */
    public function getUser(int $id) : JsonResponse
    {
        $parameters = new Parameters($_GET);

        $fields = $parameters->getFields();
        $include = $parameters->getInclude(['tasks', 'roles']);

        $user = User::where('id', $id)->first();

        if(is_null($user)) {
            return Controller::notFoundException('users', 'User', $id);
        }

        $resource = (new Resource($user, new UserSerializer()))->fields($fields);
        $resource = UserHelper::addRelationshipResource($resource, $fields, $include);

        $document = new Document($resource);
        $document->addLink('self', Config::get('app.url').'/api/users/'.$id);

        return response()->json($document);
    }

    /**
     * Создание нового пользователя
     *
     * @param Request $request Объект запроса
     * @return JsonResponse|object
     */
    public function addUser(Request $request)
    {
        $errors = User::createValidator($request->all())->errors()->getMessages();

        if(!empty($errors)) {
            return Controller::badRequestException($errors);
        }

        $newUser = User::createUser($request);

        $resource = (new Resource($newUser, new UserSerializer()));

        $document = new Document($resource);
        $document->addLink('self', Config::get('app.url').'/api/users/'.$newUser->id);

        return response()->json($document)
            ->setStatusCode(201)
            ->header('Location', Config::get('app.url').'/api/users/'.$newUser->id);
    }

    /**
     * Редактирование пользователя по ID
     *
     * @param Request $request Объект запроса
     * @param int $id Идентификатор пользователя
     * @return JsonResponse|object
     */
    public function updateUser(Request $request, int $id)
    {
        $errors = User::updateValidator($request->all())->errors()->getMessages();

        if(!empty($errors)) {
            return Controller::badRequestException($errors);
        }

        $updateUser = User::updateUser($request, $id);

        $resource = (new Resource($updateUser, new UserSerializer()));

        $document = new Document($resource);
        $document->addLink('self', Config::get('app.url').'/api/users/'.$updateUser->id);

        return response()->json($document)
            ->setStatusCode(201)
            ->header('Location', Config::get('app.url').'/api/users/'.$updateUser->id);
    }

    /**
     * Удаление пользователя по ID
     *
     * @param $id int Идентификатор пользователя
     * @return JsonResponse|Response|object
     */
    public function deleteUser(int $id) : object
    {
        $deleteUser = User::destroy($id);

        if($deleteUser == 0) {
            return Controller::notFoundException('users', 'User', $id);
        }

        return (new Response())->setStatusCode(204);
    }
}
