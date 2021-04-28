<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models\Base;

interface IActivityLog
{
    public function toActivityLogArray();
}
