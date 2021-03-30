<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

abstract class ByTypeRepository extends ModelRepository
{
    protected $type;

    public function __construct($type, $id = null)
    {
        $this->type = $type;

        parent::__construct($id);
    }

    public function query()
    {
        return parent::query()->where('type', $this->type);
    }

    public function batchInsert($attributes)
    {
        $attributes['type'] = $this->type;

        return parent::batchInsert($attributes);
    }

    protected function batchInsertAdd($attributes)
    {
        $attributes['type'] = $this->type;
        return parent::batchInsertAdd($attributes);
    }

    public function createWithAttributes(array $attributes = [])
    {
        $attributes['type'] = $this->type;
        return parent::createWithAttributes($attributes);
    }
}
