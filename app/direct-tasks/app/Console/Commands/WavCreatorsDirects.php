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
    public function __construct()
    {
        parent::__construct();
        $this->pk = '246817495';
        $this->username = 'wavcreators';
        $this->password = 'theoath';
        //$this->suspended = TRUE;
        $min = $this->currentMinute();
        if ($min < 15) {
            $this->proxy = '191.252.103.106:23128';
        }
        if ($min > 15 && $min < 30) {
            $this->proxy = '191.252.100.122:23128';
        }
        if ($min > 30 && $min < 45) {
            $this->proxy = '191.252.109.233:23128';
        }
        if ($min > 45 && $min < 59) {
            $this->proxy = '200.229.194.154:80';
        }
    }

}
