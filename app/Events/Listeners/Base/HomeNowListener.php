<?php

namespace App\Events\Listeners\Base;

use App\Utils\ClientSettings\Traits\HomeIndependentClientTrait;

abstract class HomeNowListener extends NowListener
{
    use HomeIndependentClientTrait;
}