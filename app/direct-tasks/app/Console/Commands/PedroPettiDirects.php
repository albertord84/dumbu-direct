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
    private $pk = '236116119';

    private $username = 'pbpetti';
    
    private $password = 'Pp75005310';
    
    private $instagram = NULL;
    
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
        $this->proxy = '191.252.101.188:23128';
    }

}
