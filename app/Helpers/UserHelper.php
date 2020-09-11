<?php

namespace App\Helpers;

use Tobscure\JsonApi\Resource;

/**
 * Class UserHelper
 * Хелпер для работы с сущностью User
 * @package App\Helpers
 */
class UserHelper
{
    /**
     * Определение подключать связьи с сущностями или нет
     *
     * @param $resource
     * @param $fields
     * @param $include
     * @return Resource
     */
    public static function addRelationshipResource(Resource $resource, array $fields, array $include) : Resource
    {
        if(isset($fields['users'])) {
            foreach ($fields['users'] as $item) {
                switch ($item) {
                    case 'tasks': $resource->with(['tasks']); break;
                    case 'roles': $resource->with(['roles']);
                }
            }
        }

        foreach ($include as $includeItem) {
            switch ($includeItem) {
                case 'tasks': $resource->with(['tasks']); break;
                case 'roles': $resource->with(['roles']);
            }
        }

        return $resource;
    }
}
