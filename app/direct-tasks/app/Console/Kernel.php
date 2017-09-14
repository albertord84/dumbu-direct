<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// Para tener acceso a las cosas de CodeIgniter
define('BASEPATH', __DIR__ . '/../../../system');
define('APPPATH', __DIR__ . '/../../../application');

// Para tener acceso al directorio del estanque de mensajes
define('QUEUE_PATH', APPPATH . '/logs/directs/queue/');
define('OLD_QUEUE_PATH', APPPATH . '/logs/directs/old/');

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
        '\App\Console\Commands\PedroPettiDirects',
        '\App\Console\Commands\WavCreatorsDirects',
        '\App\Console\Commands\CarmenVecchioDirects'
    ];

    protected $stopHours = [
        1,3,5,7,9,11,13,16,18,20,22,23
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $outputLog = APPPATH . '/../../messages.log';
        
        $this->delayMessages();
        
        $schedule->command('sendirects:dumbu08')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:dumbu09')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        
        // Ejemplo de envio a determinado minuto de cada hora del dia
        /*$schedule->command('sendirects:pedropetti')
            ->cron('12 * * * * *')
            ->appendOutputTo($outputLog);*/
        
        $schedule->command('sendirects:wavcreators')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:carmenvecchio')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
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
        if ( in_array($H, $this->stopHours) )
        {
            $h = array_search($H, $this->stopHours);
            printf("%s - Esperando 1h (hasta las %s:00) para reiniciar el envio...\n",
                date('r'), $this->stopHours[ $h ] + 1);
            exit(0);
        }
    }

}
