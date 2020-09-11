<?php

namespace App\Http\Controllers;

use App\Helpers\TaskHelper;
use App\Models\Task;
use App\Services\TaskSerializer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;
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
     * @return JsonResponse
     * @throws \Tobscure\JsonApi\Exception\InvalidParameterException
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

        $collection = (new Collection($tasks, new TaskSerializer()))
            ->fields($fields);

        $collection = TaskHelper::addRelationshipCollection($collection, $fields, $include);

        $document = new Document($collection);
        $document->addLink('self', 'http://example.com/api/task/');

        if(isset($_GET['page'])) {
            $document->addPaginationLinks(
                'http://example.com/api/tasks',
                ['page' => [
                    'limit'  => $limit,
                    'offset' => $offset
                ]],
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Tobscure\JsonApi\Exception\InvalidParameterException
     */
    public function getTask(int $id) : JsonResponse
    {
        $parameters = new Parameters($_GET);

        $fields = $parameters->getFields();
        $include = $parameters->getInclude(['users', 'statuses']);

        $task = Task::where('id', $id)->first();

        if(is_null($task)) {
            return response()->json(['errors' => [
                [
                    'status' => '404',
                    'source' => ['pointer' => 'tasks'],
                    'title'  => 'Not found',
                    'detail' => 'Task with ID '.$id.' not found'
                ]
            ]])->setStatusCode(404);
        }

        $resource = (new Resource($task, new TaskSerializer()))->fields($fields);

        $resource = TaskHelper::addRelationshipResource($resource, $fields, $include);

        $document = new Document($resource);
        $document->addLink('self', 'https://example.com/api/tasks/'.$id);

        return response()->json($document);
    }

    /**
     * Создание новой задачи
     *
     * @param Request $request
     * @return JsonResponse|object
     */
    public function addTask(Request$request)
    {
        $errors = Task::createValidator($request->all())->errors()->getMessages();

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

        $newTask = Task::createTask($request);

        $resource = (new Resource($newTask, new TaskSerializer()));

        $document = new Document($resource);
        $document->addLink('self', 'https://example.com/api/tasks/'.$newTask->id);

        return response()->json($document)
            ->setStatusCode(201)
            ->header('Location', 'http://ktteam-domain/api/tasks/'.$newTask->id);
    }

    public function updateTask(Request $request, int $id)
    {
        $errors = Task::updateValidator($request->all())->errors()->getMessages();

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

        $updateTask = Task::updateTask($request, $id);

        $resource = (new Resource($updateTask, new TaskSerializer()));

        $document = new Document($resource);
        $document->addLink('self', 'https://example.com/api/tasks/'.$updateTask->id);

        return response()->json($document)
            ->header('Location', 'http://ktteam-domain/api/tasks/'.$updateTask->id);
    }

    /**
     * Удаление задачи по ID
     *
     * @param $id int
     * @return \Illuminate\Http\JsonResponse|Response|object
     */
    public function deleteTask(int $id) : object
    {
        $deleteTask = Task::destroy($id);

        if($deleteTask == 0) {
            return response()->json(['errors' => [
                [
                    'status' => '404',
                    'source' => ['pointer' => 'tasks'],
                    'title'  => 'Not found',
                    'detail' => 'Task with ID '.$id.' not found'
                ]
            ]])->setStatusCode(404);
        }

        return (new Response())->setStatusCode(204);
    }
}
