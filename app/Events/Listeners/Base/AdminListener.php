<?php

namespace App\Events\Listeners\Base;

use App\Utils\ClientSettings\Traits\AdminIndependentClientTrait;

abstract class AdminListener extends Listener
{
    use AdminIndependentClientTrait;
}