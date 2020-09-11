<?php

namespace App\Helpers;

use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Resource;

/**
 * Class TaskHelper
 * Хелпер для работы сущностью Task
 * @package App\Helpers
 */
class TaskHelper
{
    /**
     * Определение подключать связи с сущностями или нет
     *
     * @param $fields
     * @param $include
     * @return Resource
     */
    public static function addRelationshipResource(Resource $resource, array $fields, array $include) : Resource
    {
        if(isset($fields['tasks'])) {
            foreach ($fields['tasks'] as $item) {
                switch ($item) {
                    case 'users': $resource->with(['users']); break;
                    case 'statuses': $resource->with(['statuses']);
                }
            }
        }

        foreach ($include as $includeItem) {
            switch ($includeItem) {
                case 'users': $resource->with(['users']); break;
                case 'statuses': $resource->with(['statuses']);
            }
        }

        return $resource;
    }

    /**
     * Определение подключать связи с сущностями или нет
     *
     * @param $collection
     * @param $fields
     * @param $include
     * @return Collection
     */
    public static function addRelationshipCollection(Collection $collection, array $fields, array $include) : Collection
    {
        if(isset($fields['tasks'])) {
            foreach ($fields['tasks'] as $item) {
                switch ($item) {
                    case 'users': $collection->with(['users']); break;
                    case 'statuses': $collection->with(['statuses']);
                }
            }
        }

        foreach ($include as $includeItem) {
            switch ($includeItem) {
                case 'users': $collection->with(['users']); break;
                case 'statuses': $collection->with(['statuses']);
            }
        }

        return $collection;
    }
}
