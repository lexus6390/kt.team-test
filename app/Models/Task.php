<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Class Task
 * @property $id int
 * @property $user_id int
 * @property $title string
 * @property $description string
 * @property $estimate int
 * @property $spent int
 * @property $status_id int
 * @property $created_at string
 * @property $updated_at string
 * @package App\Models
 */
class Task extends Model
{
    use HasFactory;

    /**
     * Имя таблицы в БД
     *
     * @var string
     */
    protected $table = 'task';

    /**
     * Атрибуты, доступные для массового заполнения
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'description', 'estimate', 'spent', 'status_id'
    ];

    /**
     * Валидация входящих данных при создании новой задачи
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function createValidator(array $data)
    {
        return Validator::make($data, [
            'user_id'     => ['required', 'integer'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'estimate'    => ['required', 'integer']
        ], [
            'required' => 'Поле :attribute должно быть заполнено',
            'max'      => 'Максимальная длина поля :attribute - 255 символов',
            'string'   => 'Значение поля :attribute должно быть строкой',
            'integer'  => 'Значение поля :attribute должно быть целым числом',
        ]);
    }

    /**
     * Создание новой задачи
     *
     * @param $data
     * @return Task |JsonResponse
     */
    public static function createTask($data)
    {
        $task = new self();
        $task->fill([
            'user_id'     => $data['user_id'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'estimate'    => $data['estimate'],
            'status_id'   => Status::STATUS_NEW_TASK
        ]);

        if(!$task->save()) {
            return Controller::internalServerError('tasks', 'Task');
        }

        return $task;
    }

    /**
     * Валидация входящих данных при редактировании задачи
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function updateValidator(array $data)
    {
        return Validator::make($data, [
            'user_id'     => ['integer'],
            'title'       => ['string', 'max:255'],
            'description' => ['string'],
            'estimate'    => ['integer'],
            'spent'       => ['integer'],
            'status_id'   => ['integer']
        ], [
            'max'     => 'Максимальная длина поля :attribute - 255 символов',
            'string'  => 'Значение поля :attribute должно быть строкой',
            'integer' => 'Значение поля :attribute должно быть целым числом',
        ]);
    }

    /**
     * Редактирование задачи по ID
     *
     * @param $data
     * @param int $id
     * @return Task|JsonResponse|object
     */
    public static function updateTask($data, int $id)
    {
        /** @var Task $task */
        $task = self::where(['id' => $id])->first();

        if(is_null($task)) {
            return Controller::notFoundException('tasks', 'Task', $id);
        }

        $task->fill([
            'user_id'     => $data['user_id'] ?? $task->user_id,
            'title'       => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'estimate'    => $data['estimate'] ?? $task->estimate,
            'spent'       => $data['spent'] ?? $task->spent,
            'status_id'   => $data['status_id'] ?? $task->status_id
        ]);

        if(!$task->save()) {
            return Controller::internalServerError('tasks', 'Task');
        }

        return $task;
    }

    /**
     * Связь один к одному с таблицей user (task.user_id=user.id)
     *
     * @return HasOne
     */
    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Связь один к одному с таблицей status (task.status_id=status.id)
     *
     * @return HasOne
     */
    public function statuses()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }
}
