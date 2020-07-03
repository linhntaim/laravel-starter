<?php

namespace App\Jobs\Base;

use Illuminate\Foundation\Bus\Dispatchable;

abstract class NowJob
{
    use Dispatchable;

    public function handle()
    {
        $this->go();
    }

    public abstract function go();

    public abstract function failed();
}
