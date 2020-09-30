<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners\Base;

abstract class AdminNowListener extends NowListener
{
    use AdminListenerTrait;
}
