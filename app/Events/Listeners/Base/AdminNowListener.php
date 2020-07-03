<?php

namespace App\Events\Listeners\Base;

abstract class AdminNowListener extends NowListener
{
    use AdminListenerTrait;
}
