<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * @property $id int
 * @property $first_name string
 * @property $last_name string
 * @property $role int
 * @property $email string
 * @property $email_verified_at string
 * @property $password string
 * @property $remember_token string
 * @property $created_at string
 * @property $updated_at string
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Название таблицы в БД
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Аттрибуты, доступные для массового заполнения
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'role', 'email', 'password'
    ];

    /**
     * Атрибуты, которые должны быть скрыты
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Атрибуты, которые следует приводить к собственным типам
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    /**
     * Валидация входящих данных при создании нового пользователя
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function createValidator(array $data)
    {
        return Validator::make($data, [
            'first_name'            => ['required', 'string', 'max:255'],
            'last_name'             => ['required', 'string', 'max:255'],
            'role'                  => ['required', 'integer', 'between:1,5'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8']
        ], [
            'required'  => 'Поле :attribute должно быть заполнено',
            'email'     => 'Передан не валидный email',
            'unique'    => 'Пользователь с указанным email уже существует',
            'max'       => 'Максимальная длина поля :attribute - 255 символов',
            'min'       => 'Минимальная длина пароля - 8 символов',
            'confirmed' => 'Пароли должны совпадать',
            'string'    => 'Значение поля :attribute должно быть строкой',
            'integer'   => 'Значение поля :attribute должно быть целым числом',
            'between'   => 'Значение поля :attribute должно быть в диапазоне от 1 до 5'
        ]);
    }

    /**
     * Создание нового пользователя
     *
     * @param $data
     * @return User|JsonResponse
     */
    public static function createUser($data)
    {
        $user = new self();
        $user->fill([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'password'   => Hash::make($data['password']),
        ]);

        if(!$user->save()) {
            return Controller::internalServerError('users', 'User');
        }

        return $user;
    }

    /**
     * Валидация входящих данных при редактировании пользователя
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function updateValidator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['string', 'max:255'],
            'last_name'  => ['string', 'max:255'],
            'email'      => ['string', 'email', 'max:255', 'unique:users'],
        ], [
            'email'  => 'Передан не валидный email',
            'unique' => 'Пользователь с указанным email уже существует',
            'string' => 'Значение поля :attribute должно быть строкой',
            'max'    => 'Максимальная длина поля :attribute - 255 символов',
        ]);
    }

    /**
     * Редактирование пользователя по ID
     *
     * @param $data
     * @param int $id
     * @return Task|JsonResponse|object
     */
    public static function updateUser($data, int $id)
    {
        /** @var User $task */
        $user = self::where(['id' => $id])->first();

        if(is_null($user)) {
            return Controller::notFoundException('users', 'User', $id);
        }

        $user->fill([
            'first_name' => $data['first_name'] ?? $user->first_name,
            'last_name'  => $data['last_name'] ?? $user->last_name,
            'email'      => $data['email'] ?? $user->email,
        ]);

        if(!$user->save()) {
            return Controller::internalServerError('users', 'User');
        }

        return $user;
    }

    /**
     * Связь один ко многим с сущностью Task (user.id=task.user_id)
     *
     * @return HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id', 'id');
    }

    /**
     * Связь один к одному с сущностью Role (user.role=role.id)
     *
     * @return HasOne
     */
    public function roles()
    {
        return $this->hasOne(Role::class, 'id', 'role');
    }
}
