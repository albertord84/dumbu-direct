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

    // Establecer para saber a partir de cuando, hacer
    // pausas en el envio cada vez mayores a fin de no
    // embotar el margen de peticiones permitidas
    protected $stopHour = 13;

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
            2,4,6,8,10,12,14,16,18,20,22
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
