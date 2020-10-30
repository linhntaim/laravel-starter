<?php

namespace App\Models\Base;

interface IProtected
{
    public static function getProtectedKey();

    public static function getProtectedValues();

    public function getProtectedValue();
}
