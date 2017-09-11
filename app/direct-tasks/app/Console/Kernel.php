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
        $this->delayMessages();
        /*$schedule->command('sendirects:dumbu08')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:dumbu09')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);*/
        $schedule->command('sendirects:pedropetti')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        /*$schedule->command('sendirects:pedropetti')
            ->cron('12 * * * * *')
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->cron('23 * * * * *')
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->cron('34 * * * * *')
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->cron('41 * * * * *')
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

    private function delayMessages()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $H = intval(date("H"));
        $stopsHour = [
            1,3,5,7,9,11,13,15,17,19,21,23
        ];
        if ( in_array($H, $stopsHour) )
        {
            $h = array_search($H, $stopsHour);
            printf("%s - Esperando 1h (hasta las %s:00) para reiniciar el envio...\n",
                date('r'), $stopsHour[ $h ] + 1);
            exit(0);
        }
    }

}
