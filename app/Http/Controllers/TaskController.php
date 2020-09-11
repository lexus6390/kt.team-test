<?php

namespace App\Http\Controllers;

use App\Helpers\TaskHelper;
use App\Models\Task;
use App\Services\TaskSerializer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Exception\InvalidParameterException;
use Tobscure\JsonApi\Parameters;
use Tobscure\JsonApi\Resource;

/**
 * Class TaskController
 * Контроллер для работы с сущностью задачи (Task)
 * @package App\Http\Controllers
 */
class TaskController extends Controller
{
    /**
     * Регистрация роутов для работы с сущностью задачи (Task)
     */
    public static function routesTask() : void
    {
        // Список задач
        Route::get('/tasks', [TaskController::class, 'getTaskList']);

        // Одна задача
        Route::get('/tasks/{id}', [TaskController::class, 'getTask']);

        // Добавление задачи
        Route::post('/tasks', [TaskController::class, 'addTask']);

        // Редактирование задачи
        Route::patch('/tasks/{id}', [TaskController::class, 'updateTask']);

        // Удаление задачи
        Route::delete('/tasks/{id}', [TaskController::class, 'deleteTask']);
    }

    /**
     * Получение списка задач с фильтрацией, сортировкой и пагинацией
     *
     * @return JsonResponse
     * @throws InvalidParameterException
     */
    public function getTaskList()
    {
        $parameters = new Parameters($_GET);

        $fields = $parameters->getFields();
        $include = $parameters->getInclude(['users', 'statuses']);
        $sort = $parameters->getSort(['estimate', 'spent']);
        $filters = $parameters->getFilter();
        $limit = $parameters->getLimit();
        $offset = $parameters->getOffset($limit);

        $tasks = Task::select('*');
        if(!empty($filters)) {
            foreach ($filters as $param => $condition) {
                $arrayCondition = explode(',',$condition);
                if(count($arrayCondition) > 0) {
                    $tasks->whereIn($param, $arrayCondition);
                } else {
                    $tasks->where($param, $condition);
                }
            }
        }

        $countTask = $tasks->count();

        if(!empty($sort)) {
            foreach ($sort as $param => $condition) {
                $tasks->orderBy($param, $condition);
            }
        }
        if(!empty($limit)) {
            $tasks->limit($limit);
        }
        if(!empty($offset)) {
            $tasks->where('id', '>', $offset);
        }
        $tasks = $tasks->get();

        $collection = (new Collection($tasks, new TaskSerializer()))->fields($fields);
        $collection = TaskHelper::addRelationshipCollection($collection, $fields, $include);

        $document = new Document($collection);
        $document->addLink('self', Config::get('app.url').'/api/task/');

        if(isset($_GET['page'])) {
            $document->addPaginationLinks(
                Config::get('app.url').'/api/tasks',
                [
                    'page' => [
                        'limit'  => $limit,
                        'offset' => $offset
                    ]
                ],
                $offset,
                $limit,
                $countTask
            );
        }

        return response()->json($document);
    }

    /**
     * Получение одной задачи по ID
     *
     * @param $id int
     * @return JsonResponse
     * @throws InvalidParameterException
     */
    public function getTask(int $id) : JsonResponse
    {
        $parameters = new Parameters($_GET);

        $fields = $parameters->getFields();
        $include = $parameters->getInclude(['users', 'statuses']);

        $task = Task::where('id', $id)->first();

        if(is_null($task)) {
            return Controller::notFoundException('tasks', 'Task', $id);
        }

        $resource = (new Resource($task, new TaskSerializer()))->fields($fields);
        $resource = TaskHelper::addRelationshipResource($resource, $fields, $include);

        $document = new Document($resource);
        $document->addLink('self', Config::get('app.url').'/api/tasks/'.$id);

        return response()->json($document);
    }

    /**
     * Создание новой задачи
     *
     * @param Request $request Объект запроса
     * @return JsonResponse|object
     */
    public function addTask(Request $request) : JsonResponse
    {
        $errors = Task::createValidator($request->all())->errors()->getMessages();

        if(!empty($errors)) {
            return Controller::badRequestException($errors);
        }

        $newTask = Task::createTask($request);

        $resource = (new Resource($newTask, new TaskSerializer()));

        $document = new Document($resource);
        $document->addLink('self', Config::get('app.url').'/api/tasks/'.$newTask->id);

        return response()->json($document)
            ->setStatusCode(201)
            ->header('Location', Config::get('app.url').'/api/tasks/'.$newTask->id);
    }

    /**
     * Редактирование задачи по ID
     *
     * @param Request $request Объект запроса
     * @param int $id Идентификатор задачи
     * @return JsonResponse|object
     */
    public function updateTask(Request $request, int $id) : JsonResponse
    {
        $errors = Task::updateValidator($request->all())->errors()->getMessages();

        if(!empty($errors)) {
            return Controller::badRequestException($errors);
        }

        $updateTask = Task::updateTask($request, $id);

        $resource = (new Resource($updateTask, new TaskSerializer()));

        $document = new Document($resource);
        $document->addLink('self', Config::get('app.url').'/api/tasks/'.$updateTask->id);

        return response()->json($document)
            ->header('Location', Config::get('app.url').'/api/tasks/'.$updateTask->id);
    }

    /**
     * Удаление задачи по ID
     *
     * @param $id int Идентификатор задачи
     * @return JsonResponse|Response|object
     */
    public function deleteTask(int $id) : object
    {
        $deleteTask = Task::destroy($id);

        if($deleteTask == 0) {
            return Controller::notFoundException('tasks', 'Task', $id);
        }

        return (new Response())->setStatusCode(204);
    }
}
