<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
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
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'role', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function roles()
    {
        return $this->hasOne(Role::class, 'id', 'role');
    }

    /**
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
            'required'     => 'Поле :attribute должно быть заполнено',
            'email'        => 'Передан не валидный email',
            'unique'       => 'Пользователь с указанным email уже существует',
            'max'          => 'Максимальная длина поля :attribute - 255 символов',
            'min'          => 'Минимальная длина пароля - 8 символов',
            'confirmed'    => 'Пароли должны совпадать',
            'string'       => 'Значение поля :attribute должно быть строкой',
            'integer'      => 'Значение поля :attribute должно быть целым числом',
            'between'      => 'Значение поля :attribute должно быть в диапазоне от 1 до 5'
        ]);
    }

    /**
     * @param $data
     * @return User|\Illuminate\Http\JsonResponse
     */
    public static function createUser($data)
    {
        $user = new self();
        $user->fill([
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'email'            => $data['email'],
            'role'             => $data['role'],
            'password'         => Hash::make($data['password']),
        ]);

        if(!$user->save()) {
            return response()
                ->json(['errors' => [
                    [
                        'status' => '500',
                        'source' => ['pointer' => 'users'],
                        'title'  => 'Internal Server Error',
                        'detail' => 'Ошибка при сохранении сущности User'
                    ]
                ]])
                ->setStatusCode(500);
        }

        return $user;
    }

    /**
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
            'email'        => 'Передан не валидный email',
            'unique'       => 'Пользователь с указанным email уже существует',
            'string'       => 'Значение поля :attribute должно быть строкой',
            'max'          => 'Максимальная длина поля :attribute - 255 символов',
        ]);
    }

    /**
     * @param $data
     * @param int $id
     * @return Task|\Illuminate\Http\JsonResponse|object
     */
    public static function updateUser($data, int $id)
    {
        /** @var User $task */
        $user = self::where(['id' => $id])->first();

        if(is_null($user)) {
            return response()
                ->json(['errors' => [
                    [
                        'status' => '404',
                        'source' => ['pointer' => 'users'],
                        'title'  => 'Not found',
                        'detail' => 'Запись сущности User с переданным ID не найдена'
                    ]
                ]])
                ->setStatusCode(404);
        }

        $user->fill([
            'first_name'  => $data['first_name'] ?? $user->first_name,
            'last_name'   => $data['last_name'] ?? $user->last_name,
            'email'       => $data['email'] ?? $user->email,
        ]);

        if(!$user->save()) {
            return response()
                ->json(['errors' => [
                    [
                        'status' => '500',
                        'source' => ['pointer' => 'users'],
                        'title'  => 'Internal Server Error',
                        'detail' => 'Ошибка при сохранении сущности User'
                    ]
                ]])
                ->setStatusCode(500);
        }

        return $user;
    }
}
