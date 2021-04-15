<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Jobs\Base;

use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Traits\IndependentClientTrait;
use Illuminate\Foundation\Bus\Dispatchable;

abstract class NowJob
{
    use ClassTrait, Dispatchable, IndependentClientTrait;

    protected static function __transCurrentModule()
    {
        return 'job';
    }

    public function handle()
    {
        $this->independentClientApply();
        $this->go();
    }

    public abstract function go();

    public abstract function failed();
}
