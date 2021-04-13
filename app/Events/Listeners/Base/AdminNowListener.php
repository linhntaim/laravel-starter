<?php

namespace App\Events\Listeners\Base;

use App\Utils\ClientSettings\Traits\AdminIndependentClientTrait;

abstract class AdminNowListener extends NowListener
{
    use AdminIndependentClientTrait;
}