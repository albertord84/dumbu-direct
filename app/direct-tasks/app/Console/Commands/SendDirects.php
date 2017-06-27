<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDirects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendirects:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages to selected reference profiles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = date("Y-m-d H:i:s", time());
        $creds = array();
        $username = NULL;
        $password = NULL;
        $netProxy = FALSE;
        $recip='alberto_dreyes';
        $debug = false;
        $truncatedDebug = false;
        set_time_limit(0);
        date_default_timezone_set('UTC');
        try {
          require_once __DIR__.'/../../../../../vendor/autoload.php';
        } catch (\Exception $e) {
          $m = $e->getMessage();
          echo "$date -- Something went wrong trying to include Instagram library dependencies: $m\n";
          exit(0);
        }
        try {
            $instag_creds_file = __DIR__.'/../../../../../web/instagram_credentials';
            $_creds = file_get_contents($instag_creds_file);
            $creds = explode(':', $_creds);
            $username = $creds[0];
            $password = $creds[1];
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get Instagram credentials: $m\n";
            exit(0);
        }
        try {
            $proxy_data_file = __DIR__.'/../../../../../web/net_proxy';
            if (file_exists($proxy_data_file)) {
                $netProxy = file_get_contents($proxy_data_file);
            }
            if (empty($netProxy) || trim($netProxy)=='') {
                $netProxy = FALSE;
            }
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get the network proxy data: $m\n";
            exit(0);
        }
        $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
        if ($netProxy) $ig->client->setProxy($netProxy);
        try {
            $ig->setUser($username, $password);
            $ig->login();
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to login: $m\n";
            exit(0);
        }
        $uId = NULL;
        try {
            $uId = $ig->getUsernameId($recip);
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get the username id of $recip: $m\n";
            exit(0);
        }
        try {
            $msg = "$date -- Mensaje automatizado...";
            $ig->directMessage($uId, $msg);
            echo "$date -- Mensaje enviado a $recip: $msg\n";
            exit(0);
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to send the message to $recip: $m\n";
            exit(0);
        }
        try {
            $ig->logout();
            exit(0);
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to logout: $m\n";
            exit(0);
        }
    }
}
