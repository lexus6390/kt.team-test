<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * @var string
     */
    protected $table = 'task';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'description', 'estimate', 'spent', 'status_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function statuses()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    /**
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
            'required'     => 'Поле :attribute должно быть заполнено',
            'max'          => 'Максимальная длина поля :attribute - 255 символов',
            'string'       => 'Значение поля :attribute должно быть строкой',
            'integer'      => 'Значение поля :attribute должно быть целым числом',
        ]);
    }

    /**
     * @param $data
     * @return Task |\Illuminate\Http\JsonResponse
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
            return response()
                ->json(['errors' => [
                    [
                        'status' => '500',
                        'source' => ['pointer' => 'tasks'],
                        'title'  => 'Internal Server Error',
                        'detail' => 'Ошибка при сохранении сущности Task'
                    ]
                ]])
                ->setStatusCode(500);
        }

        return $task;
    }

    /**
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
            'max'          => 'Максимальная длина поля :attribute - 255 символов',
            'string'       => 'Значение поля :attribute должно быть строкой',
            'integer'      => 'Значение поля :attribute должно быть целым числом',
        ]);
    }

    /**
     * @param $data
     * @param int $id
     * @return Task|\Illuminate\Http\JsonResponse|object
     */
    public static function updateTask($data, int $id)
    {
        /** @var Task $task */
        $task = self::where(['id' => $id])->first();

        if(is_null($task)) {
            return response()
                ->json(['errors' => [
                    [
                        'status' => '404',
                        'source' => ['pointer' => 'tasks'],
                        'title'  => 'Not found',
                        'detail' => 'Запись сущности Task с переданным ID не найдена'
                    ]
                ]])
                ->setStatusCode(404);
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
            return response()
                ->json(['errors' => [
                    [
                        'status' => '500',
                        'source' => ['pointer' => 'tasks'],
                        'title'  => 'Internal Server Error',
                        'detail' => 'Ошибка при сохранении сущности Task'
                    ]
                ]])
                ->setStatusCode(500);
        }

        return $task;
    }
}
