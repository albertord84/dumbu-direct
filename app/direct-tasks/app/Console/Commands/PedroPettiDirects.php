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
        // 191.252.110.140
        // 191.252.103.137
        // 191.252.111.93
        $this->proxy = '191.252.110.140:23128';
    }

}
