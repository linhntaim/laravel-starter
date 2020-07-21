<?php

namespace App\Console;

use App\Console\Schedules\Schedule as AppSchedule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    protected $schedules = [
        'minutely' => [
        ],
    ];

    protected function getSchedulesByGroup($group)
    {
        return isset($this->schedules[$group]) ? $this->schedules[$group] : [];
    }

    /**
     * @param $class
     * @return AppSchedule
     */
    public function getSchedule($class)
    {
        return new $class();
    }

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            foreach ($this->getSchedulesByGroup('minutely') as $scheduleClass) {
                $this->getSchedule($scheduleClass)
                    ->withKernel($this)
                    ->handle();
            }
        })->everyMinute()->name('minutely')->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
