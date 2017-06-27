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
        $captionText = '';
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
        try {
            $uId = $ig->getUsernameId($recip);
            $ig->directMessage($uId, "Sigo enviando cada 5min... Lo del Proxy es sencillo... Solo hay que poner un archivo en la raiz del directorio web, que se llame net_proxy. Sin extension .txt ni nada, solo que se llame asi... Debe contener nada mas esto: ip.del.proxy.com:puerto. Si ese archivo existe en ese directorio, las peticiones se haran a traves de ese proxy. Si no existe el archivo, se haran sin usar proxy...");
            echo "$date -- Mensaje enviado a $recip\n";
            $ig->logout();
            exit(0);
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to post to $recip: $m\n";
        }
    }
}
