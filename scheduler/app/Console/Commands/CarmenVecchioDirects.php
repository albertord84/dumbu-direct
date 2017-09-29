<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CarmenVecchioDirects extends DirectsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendirects:carmenvecchio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages to carmenvecchio followers';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function handle()
    {
        $this->pk = '55324697';
        $this->username = 'carmenvecchio';
        $this->password = 'filippo88';
        //$this->suspended = TRUE;
        $proxyNumber = mt_rand(0,5);
        $this->setProxyNumber($proxyNumber);
        
        parent::handle();
    }

}
