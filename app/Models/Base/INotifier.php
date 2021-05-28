<?php

namespace App\Models\Base;

interface INotifier extends IAvatar
{
    public function getKey();

    public static function findByKey($key);
}