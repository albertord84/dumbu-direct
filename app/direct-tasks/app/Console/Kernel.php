<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// Para tener acceso al directorio del estanque de mensajes
define('BASEPATH', __DIR__ . '/../../../system');
define('APPPATH', __DIR__ . '/../../../application');

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\Dumbu08Directs',
        '\App\Console\Commands\Dumbu09Directs',
        '\App\Console\Commands\PedroPettiDirects'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $outputLog = __DIR__ . '/../../../../messages.log';
        $schedule->command('sendirects:dumbu08')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        /*$schedule->command('sendirects:dumbu09')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->cron('3 * * * * *')
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->cron('13 * * * * *')
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->cron('20 * * * * *')
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->cron('32 * * * * *')
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->cron('44 * * * * *')
            ->appendOutputTo($outputLog);*/
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
