<?php


namespace App\Services;

use Tobscure\JsonApi\AbstractSerializer;

/**
 * Class RoleSerializer
 * @package App\Services
 */
class RoleSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'roles';

    /**
     * @param mixed $role
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($role, array $fields = null) : array
    {
        return [
            'role_name'  => $role->role_name,
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at
        ];
    }
}
