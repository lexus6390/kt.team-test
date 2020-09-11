<?php

namespace App\Services;

use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\Resource;

/**
 * Class TaskSerializer
 * @package App\Services
 */
class TaskSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'tasks';

    /**
     * @param mixed $task
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($task, array $fields = null) : array
    {
        return [
            'title'       => $task->title,
            'description' => $task->description,
            'estimate'    => $task->estimate,
            'spent'       => $task->spent,
            'created_at'  => $task->created_at,
            'updated_at'  => $task->updated_at
        ];
    }

    /**
     * @param $task
     * @return Relationship
     */
    public function users($task) : Relationship
    {
        $element = new Resource($task->users, new UserSerializer());
        return new Relationship($element);
    }

    /**
     * @param $task
     * @return Relationship
     */
    public function statuses($task) : Relationship
    {
        $element = new Resource($task->statuses, new StatusSerializer());
        return new Relationship($element);
    }
}
