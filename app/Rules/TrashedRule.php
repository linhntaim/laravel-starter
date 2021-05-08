<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Rules;

use App\Rules\Base\Rule;

class TrashedRule extends Rule
{
    protected $modelClass;

    protected $modelAttribute;

    public function __construct()
    {
        parent::__construct();

        $this->name = 'not_trashed';
    }

    public function with($modelClass, $attribute = '')
    {
        $this->modelClass = $modelClass;
        $this->modelAttribute = $attribute;

        return $this;
    }

    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        if (empty($this->modelAttribute)) {
            $this->modelAttribute = $attribute;
        }

        $query = call_user_func($this->modelClass . '::query');
        $query->onlyTrashed()
            ->where($this->modelAttribute, $value);
        return $query->count() > 0;
    }
}
