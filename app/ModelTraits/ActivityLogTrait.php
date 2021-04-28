<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelTraits;

use Illuminate\Support\Arr;

trait ActivityLogTrait
{
    public function getActivityLogLogHidden()
    {
        return $this->activityLogHidden;
    }

    public function toActivityLogArray()
    {
        return $this->toActivityLogArrayFrom(Arr::except($this->toArray(), $this->getActivityLogLogHidden()));
    }

    public function toActivityLogArrayFrom($fromArray)
    {
        $array = [];
        foreach ($fromArray as $key => $value) {
            $array[sprintf('%s.%s', get_class($this), $key)] = $value;
        }
        return $array;
    }
}
