<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

interface INotifier extends IAvatar
{
    public function getKey();

    public static function findByKey($key);
}