<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PedroPettiDirects extends DirectsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendirects:pedropetti';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages to pedropetti followers';
    
    // 5787797919 = pedropetti
    // 236116119 = pbpetti
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->pk = '236116119';
        $this->username = 'pbpetti';
        $this->password = 'Pp75005310';
        $this->suspended = FALSE;
        $min = $this->currentMinute();
        if ($min < 15) {
            $this->setProxyNumber(0);
        }
        if ($min > 15 && $min < 30) {
            $this->setProxyNumber(1);
        }
        if ($min > 30 && $min < 45) {
            $this->setProxyNumber(2);
        }
        if ($min > 45 && $min < 59) {
            $this->setProxyNumber(3);
        }
    }

}
