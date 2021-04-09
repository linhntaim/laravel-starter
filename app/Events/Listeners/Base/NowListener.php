<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners\Base;

use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Traits\IndependentClientTrait;

abstract class NowListener
{
    use ClassTrait, IndependentClientTrait;

    protected static function __transCurrentModule()
    {
        return 'listener';
    }

    public function handle($event)
    {
        $this->independentClientApply();
        $this->go($event);
    }

    protected abstract function go($event);
}
