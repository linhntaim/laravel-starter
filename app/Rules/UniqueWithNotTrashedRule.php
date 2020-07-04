<?php

namespace App\Rules;

use App\Rules\Base\Rule;

class UniqueWithNotTrashedRule extends Rule
{
    protected $modelClass;
    protected $modelAttribute;
    protected $ignoredId;
    protected $ignoredIdColumn;

    public function __construct()
    {
        parent::__construct();

        $this->name = 'unique_not_trashed';
    }

    public function with($modelClass, $attribute = '')
    {
        $this->modelClass = $modelClass;
        $this->modelAttribute = $attribute;

        return $this;
    }

    public function ignore($id, $idColumn = null)
    {
        $this->ignoredId = $id;
        $this->ignoredIdColumn = empty($idColumn) ? 'id' : $idColumn;

        return $this;
    }

    protected function query()
    {
        return call_user_func($this->modelClass . '::query');
    }

    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        if (empty($this->modelAttribute)) {
            $this->modelAttribute = $attribute;
        }

        $query = $this->query()
            ->onlyTrashed()
            ->where($this->modelAttribute, $value);
        if ($query->count() > 0) {
            $this->name = 'not_trashed';
            return false;
        }

        $query = $this->query()
            ->where($this->modelAttribute, $value);
        if (!empty($this->ignoredId)) {
            $query->where($this->ignoredIdColumn, '<>', $this->ignoredId);
        }
        if ($query->count() > 0) {
            $this->transPath = 'validation';
            $this->name = 'unique';
            return false;
        }
        return true;
    }
}
