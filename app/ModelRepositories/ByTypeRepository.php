<?php

namespace App\ModelRepositories;

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
        return $this->rawQuery()->where('type', $this->type);
    }

    public function batchInsert($attributes)
    {
        $attributes['type'] = $this->type;

        return parent::batchInsert($attributes);
    }

    public function batchInsertWithIgnore($attributes)
    {
        $attributes['type'] = $this->type;

        return parent::batchInsertWithIgnore($attributes);
    }

    public function createWithAttributes(array $attributes = [])
    {
        $attributes['type'] = $this->type;
        return parent::createWithAttributes($attributes);
    }
}
