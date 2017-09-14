<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Dumbu09Directs extends DirectsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendirects:dumbu09';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages to dumbu.09 followers';
    
    // 4542814483 = dumbu.09
    // 4492293740 = dumbu.08
    // 236116119 = pbpetti
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->pk = '4542814483';
        $this->username = 'dumbu.09';
        $this->password = 'dumbu2017';
        $this->suspended = FALSE;
        $min = $this->currentMinute();
        //$proxyNumber = mt_rand(0,5);
        $proxyNumber = 9;
        $this->setProxyNumber($proxyNumber);
    }

}
