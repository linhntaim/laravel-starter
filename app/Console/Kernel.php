<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console;

use App\Console\Schedules\Base\Schedule as AppSchedule;
use App\Console\Schedules\TestCommandSchedule;
use App\Console\Schedules\TestShellSchedule;
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
        [
            'frequencies' => [
                'everyMinute',
            ],
            'schedules' => [
                TestCommandSchedule::class,
                TestShellSchedule::class,
            ],
        ],
    ];

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
     * @throws
     */
    protected function schedule(Schedule $schedule)
    {
        $scheduledNames = [];
        $getScheduledName = function ($name) use (&$scheduledNames) {
            if (in_array($name, $scheduledNames)) {
                $i = 0;
                while (($suffixName = $name . '_' . (++$i)) && in_array($suffixName, $scheduledNames)) ;
                $name = $suffixName;
            }
            $scheduledNames[] = $name;
            return $name;
        };
        foreach ($this->schedules as $scheduleDefinition) {
            if (empty($scheduleDefinition['frequencies']) || empty($scheduleDefinition['schedules'])) continue;

            $called = $schedule->call(function () use ($scheduleDefinition) {
                foreach ($scheduleDefinition['schedules'] as $scheduleClass) {
                    $this->getSchedule($scheduleClass)
                        ->withKernel($this)
                        ->handle();
                }
            });
            $names = [];
            foreach ($scheduleDefinition['frequencies'] as $key => $value) {
                if (is_int($key)) {
                    $method = $value;
                    $parameters = [];
                } else {
                    $method = $key;
                    $parameters = $value;
                }
                $names[] = $method;
                $called = call_user_func_array([$called, $method], $parameters);
            }
            $called->name($getScheduledName(implode('_', $names)))
                ->onOneServer()
                ->runInBackground()
                ->withoutOverlapping();
        }
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
