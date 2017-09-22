<?php

namespace App\Console;

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
        '\App\Console\Commands\Dumbu08Directs',
        '\App\Console\Commands\Dumbu09Directs',
        '\App\Console\Commands\PedroPettiDirects',
        '\App\Console\Commands\WavCreatorsDirects',
        '\App\Console\Commands\CarmenVecchioDirects'
    ];

    protected $stopHours = [];
    
    protected $outputLog = NULL;
    
    protected function init()
    {
        set_time_limit(0);

        $this->loadConfig();

        require_once ROOT_DIR . '/vendor/autoload.php';

        date_default_timezone_set(TIMEZONE);

        $this->outputLog = MESSAGES_LOG;

        $this->loadStopHours();
    }
    
    protected function loadConfig()
    {
        $contants = include base_path() . '/../config.php';
        foreach ($contants as $contant => $value) {
            defined($contant) OR define($contant, $value);
        }
    }

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
        $this->init();
        if ($this->delayMessages()) {
            return;
        }

        $schedule->command('sendirects:dumbu08')
            ->everyThirtyMinutes()
            ->appendOutputTo($this->outputLog);
        $schedule->command('sendirects:dumbu09')
            ->everyThirtyMinutes()
            ->appendOutputTo($this->outputLog);
        $schedule->command('sendirects:pedropetti')
            ->everyThirtyMinutes()
            ->appendOutputTo($this->outputLog);
        
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
        $H = intval(date("H"));
        if ( in_array($H, $this->stopHours) )
        {
            $h = array_search($H, $this->stopHours);
            printf("%s - Esperando hasta las %s:00 para reiniciar el envio...\n",
                date('r'), $this->stopHours[ $h ] + 1);
            return TRUE;
        }
    }

}
