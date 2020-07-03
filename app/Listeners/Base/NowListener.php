<?php

namespace App\Listeners\Base;

use App\Utils\ClassTrait;

abstract class NowListener
{
    use ClassTrait;

    protected static function __transCurrentModule()
    {
        return 'listener';
    }

    public function handle($event)
    {
        $this->go($event);
    }

    protected abstract function go($event);
}
