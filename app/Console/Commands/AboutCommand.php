<?php

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
