<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// Para tener acceso a las cosas de CodeIgniter
define('BASEPATH', __DIR__ . '/../../../app/system');
define('APPPATH', __DIR__ . '/../../../app/application');

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
    
    public function __construct() {
        parent::__construct();
        
        $this->loadStopHours();
    }
    
    protected function loadStopHours()
    {
        $stopHours = trim(file_get_contents(APPPATH . '/../../stop_hours'));
        $this->stopHours = explode(',', $stopHours);
    }

    protected function delayMessages()
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
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
