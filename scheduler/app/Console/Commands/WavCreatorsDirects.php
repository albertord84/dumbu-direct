<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WavCreatorsDirects extends DirectsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendirects:wavcreators';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages to wavcreators followers';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function handle()
    {
        $this->pk = '246817495';
        $this->username = 'wavcreators';
        $this->password = 'theoath';
        $this->suspended = TRUE;
        $proxyNumber = mt_rand(0,5);
        $this->setProxyNumber($proxyNumber);
        
        parent::handle();
    }

}