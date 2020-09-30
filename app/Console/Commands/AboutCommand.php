<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use Illuminate\Foundation\Application;

class AboutCommand extends Command
{
    protected $signature = 'about';

    protected function go()
    {
        $this->warn('Laravel: v' . Application::VERSION);
    }
}
