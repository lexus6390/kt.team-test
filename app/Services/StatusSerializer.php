<?php

namespace App\Services;

use Tobscure\JsonApi\AbstractSerializer;

/**
 * Class StatusSerializer
 * @package App\Services
 */
class StatusSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'statuses';

    /**
     * @param mixed $status
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($status, array $fields = null) : array
    {
        return [
            'status_name'  => $status->status_name,
            'created_at'   => $status->created_at,
            'updated_at'   => $status->updated_at
        ];
    }
}
