<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// Para tener acceso a directorios
define('ROOT_DIR', __DIR__ . '/../../..');

// Para tener acceso a las cosas de CodeIgniter
define('BASEPATH', ROOT_DIR . '/app/system');
define('APPPATH', ROOT_DIR . '/app/application');

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

    protected $stopHours = [];
    
    protected function loadStopHours()
    {
        $stopHours = file_get_contents(ROOT_DIR . '/stop_hours');
        $this->stopHours = explode(',', trim($stopHours));
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $outputLog = APPPATH . '/../../messages.log';
        
        $this->loadStopHours();
        $this->delayMessages();
        
        $schedule->command('sendirects:dumbu08')
            ->everyThirtyMinutes()
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:dumbu09')
            ->everyThirtyMinutes()
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:pedropetti')
            ->everyThirtyMinutes()
            ->appendOutputTo($outputLog);
        
        // Ejemplo de envio a determinado minuto de cada hora del dia
        /*$schedule->command('sendirects:pedropetti')
            ->cron('12 * * * * *')
            ->appendOutputTo($outputLog);*/
        
        /*$schedule->command('sendirects:wavcreators')
            ->everyTenMinutes()
            ->appendOutputTo($outputLog);
        $schedule->command('sendirects:carmenvecchio')
            ->everyTenMinutes()
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
        if ( in_array($H, $this->stopHours) )
        {
            $h = array_search($H, $this->stopHours);
            printf("%s - Esperando 1h (hasta las %s:00) para reiniciar el envio...\n",
                date('r'), $this->stopHours[ $h ] + 1);
            exit(0);
        }
    }

}
