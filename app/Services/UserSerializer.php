<?php

namespace App\Services;

use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\Resource;

/**
 * Class UserSerializer
 * @package App\Services
 */
class UserSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'users';

    /**
     * @param mixed $user
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($user, array $fields = null) : array
    {
        return [
            'first_name'        => $user->first_name,
            'last_name'         => $user->last_name,
            'email'             => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'remember_token'    => $user->remember_token,
            'created_at'        => $user->created_at,
            'updated_at'        => $user->updated_at
        ];
    }

    /**
     * @param $user
     * @return Relationship
     */
    public function tasks($user) : Relationship
    {
        $element = new Collection($user->tasks, new TaskSerializer());
        return new Relationship($element);
    }

    /**
     * @param $user
     * @return Relationship
     */
    public function roles($user) : Relationship
    {
        $element = new Resource($user->roles, new RoleSerializer());
        return new Relationship($element);
    }
}
