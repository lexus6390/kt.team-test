<?php

namespace App\Services;

use Tobscure\JsonApi\AbstractSerializer;

/**
 * Class StatusSerializer
 * Сериализатор сущности Status
 * @package App\Services
 */
class StatusSerializer extends AbstractSerializer
{
    /**
     * Название сущности в json
     *
     * @var string
     */
    protected $type = 'statuses';

    /**
     * Получение атрибутов
     *
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
