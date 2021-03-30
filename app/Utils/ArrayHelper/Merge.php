<?php

namespace App\Utils\ArrayHelper;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Merge implements IAction
{
    protected $result;

    public function __construct($array1, $array2, $maxDepths = [])
    {
        $this->result = $this->merge($array1, $array2, $maxDepths);
    }

    protected function merge($array1, $array2, $maxDepths = [], $depth = '')
    {
        foreach ($array2 as $key => $value) {
            $currentDepth = $depth ? $depth . '.' . $key : $key;
            if (is_array($value)
                && isset($array1[$key])
                && is_array($array1[$key])
                && (Arr::isAssoc($array1[$key]) || Arr::isAssoc($value))
                && !Str::is($maxDepths, $currentDepth)) {
                $array1[$key] = $this->merge($array1[$key], $value, $maxDepths, $currentDepth);
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    public function getResult()
    {
        return $this->result;
    }
}
