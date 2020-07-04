<?php

namespace App\Utils;

trait GuardArrayTrait
{
    protected function guardEmptyValueOfAssocArray($array, $keys)
    {
        foreach ($keys as $key) {
            if (empty($array[$key])) {
                $array[$key] = null;
            }
        }
        return $array;
    }

    protected function guardEmptyAssocArray($array)
    {
        return empty($array) ? null : $array;
    }
}