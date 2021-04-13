<?php

namespace App\Events\Listeners\Base;

use App\Utils\ClientSettings\Traits\HomeIndependentClientTrait;

abstract class HomeListener extends Listener
{
    use HomeIndependentClientTrait;
}