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

    private function d_guid()
    {
        return 'd_' . strtolower( sprintf('%04X%04X%04X%04X%04X',
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
            mt_rand(16384, 20479), mt_rand(32768, 49151)) );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //if (TRUE) return;
        $date = $this->getLocalDate();
        $username = NULL;
        $password = NULL;
        $netProxy = FALSE;
        $recip='dumbu.08';
        $debug = false;
        $truncatedDebug = false;
        set_time_limit(0);
        date_default_timezone_set('UTC');
        
        $ds = $this->getDirStore();
        if ($this->hasDirStruct()) {
            echo "Existe estructura de almacen de los directs \"$ds\"...\n";
        }
        else {
            echo "No existe estructura de almacen de los directs...\n";
            echo "Creando estructura del almacen...\n";
            $this->createDirectsStoreDir();
        }
        
        $this->getAutoloader();
        
        $this->getInstagCreds($username, $password);
        
        $this->setProxy($netProxy);
        
        $ig = $this->getInstagram($debug, $truncatedDebug);
        
        if ($netProxy) {
            $this->setClientProxy($ig, $netProxy);
        }
        
        $this->setUserCredentials($ig, $username, $password);
        
        $uId = $this->getUserId($ig, $recip);
        
        try {
            $c = 10;
            $msg = "Cantidad de mensajes establecida a $c... Envio %d de %d...";
            for($i = 0; $i < $c; $i++) {
                $g = $this->d_guid();
                $m = "$date -- $g / " . sprintf($msg, $i + 1, $c);
                $ig->directMessage($uId, $m);
                echo "$date -- Mensaje enviado a $recip: \"$m\"\n";
            }
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
    
    private function getUserId($instagram, $name)
    {
        $date = $this->getLocalDate();
        try {
            $uId = $instagram->getUsernameId($name);
            return $uId;
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get the username id of $name: $m\n";
            exit(0);
        }

    }

    private function setUserCredentials(&$instagram, $username, $password)
    {
        $date = $this->getLocalDate();
        try {
            $instagram->setUser($username, $password);
            $instagram->login();
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to login: $m\n";
            exit(0);
        }
    }


    private function setClientProxy(&$instagram, $netProxy)
    {
        $instagram->client->setProxy($netProxy);
    }

        /**
     * Devuelve verdadero o falso si esta creada la estructura de
     * directorios donde se irán guardando los directs.
     * 
     * @return boolean
     */
    private function hasDirStruct()
    {
        $ds = $this->getDirStore();
        if (!$ds) {
            return FALSE;
        }
        if (file_exists($ds)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Devuelve el directorio donde se irán guardando los directs.
     * 
     * @return string
     */
    private function getDirStore()
    {
        $ds = config('app.directs', FALSE);
        if (!$ds) {
            return FALSE;
        }
        $store = isset($ds['store']);
        if ($store) {
            return $ds['store'];
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Create directorio donde se almacenan los directs
     */
    private function createDirectsStoreDir()
    {
        $ds = config('app.directs');
        $dir = $ds['store'];
        
        try {
            mkdir($dir);
            echo "Creado directorio de almacen de los directs.\n";
        } catch (Exception $e) {
            $m = $e->getTraceAsString();
            echo "No se pudo crear directorio de almacen de los directs: $m\n";
        }

        try {
            mkdir($dir . '/queued');
            echo "Creado directorio de los directs que estan activos.\n";
        } catch (Exception $e) {
            $m = $e->getTraceAsString();
            echo "No se pudo crear directorio de los directs activos: $m\n";
        }
        
        try {
            mkdir($dir . '/old');
            echo "Creado directorio de los directs que ya no estan activos.\n";
        } catch (Exception $e) {
            $m = $e->getTraceAsString();
            echo "No se pudo crear directorio de los directs enviados: $m\n";
        }
    }
    
    /**
     * Carga el gestor de dependencias para hacer referencia a
     * la API de Instagram.
     */
    private function getAutoloader()
    {
        $date = $this->getLocalDate();
        try {
            require_once __DIR__.'/../../../../../vendor/autoload.php';
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to include Instagram library dependencies: $m\n";
            exit(0);
        }
    }
    
    /**
     * Encuentra el archivo donde estan las credenciales de Instagram
     * usadas para enviar directs y las establece para posterior uso
     * en las variables pasadas como parametro.
     */
    private function getInstagCreds(&$username, &$password)
    {
        $date = $this->getLocalDate();
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

    }
    
    private function setProxy(&$netProxy)
    {
        $date = $this->getLocalDate();
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
    }

    private function getInstagram($debug, $truncatedDebug)
    {
        return new \InstagramAPI\Instagram($debug, $truncatedDebug);
    }

    private function getLocalDate()
    {
        $date_cmd = `date "+%F %r"`;
        $date = trim($date_cmd);
        return $date;
    }
}
