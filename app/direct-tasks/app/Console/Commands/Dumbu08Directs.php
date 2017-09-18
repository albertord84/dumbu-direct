<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Dumbu08Directs extends DirectsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendirects:dumbu08';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages to dumbu.08 followers';
    
    // 4542814483 = dumbu.09
    // 4492293740 = dumbu.08
    // 5787797919 = pedropetti
    // 236116119 = pbpetti
    //private $pk = '4492293740';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->pk = '4492293740';
        $this->username = 'dumbu.08';    
        $this->password = 'Sorvete69';
        $this->suspended = FALSE;
        $min = $this->currentMinute();
        //$proxyNumber = mt_rand(0,5);
        $proxyNumber = 6;
        $this->setProxyNumber($proxyNumber);
    }

    
}
