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
        set_time_limit(0);
        
        $this->handleDirectsStore();
        $this->getAutoloader();
        $this->getInstagCreds($username, $password);
        $this->getProxy($netProxy);
        $instagram = $this->getInstagram(FALSE, FALSE);
        
        if ($netProxy) {
            $this->setClientProxy($instagram, $netProxy);
        }
        
        $this->loginToInstagram($instagram, $username, $password);
        $uid = $this->getUserId($instagram, $recip);
        $this->sendMessage($instagram, $uid, "Hola, ¿cómo estás?");
        $this->logoutInstagram($instagram);
    }
    
    private function handleDirectsStore()
    {
        $ds = $this->getDirStore();
        if ($this->hasDirStruct()) {
            echo "Existe estructura de almacen de los directs \"$ds\"...\n";
        }
        else {
            echo "No existe estructura de almacen de los directs...\n";
            echo "Creando estructura del almacen...\n";
            $this->createDirectsStoreDir();
        }

    }

    /**
     * Cierra la sesión en Instagram. Es necesario hacer esto aunque
     * la API aconseja que no se haga. Si se deja la sesión abierta,
     * puede ocurrir que el usuario siga conectado desde el cliente
     * web.
     * 
     * @param \InstagramAPI\Instagram $instagram Instancia del objeto Instagram
     */
    private function logoutInstagram($instagram)
    {
        $date = $this->getLocalDate();
        try {
            $u = $instagram->getSelfUserInfo()->user->username;
            echo "Cerrando sesión para el usuario: $u...\n";
            $instagram->logout();
            echo "Sesión cerrada para el usuario: $u\n";
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to logout: $m\n";
            exit(0);
        }
    }

    /**
     * Envia un mensaje al usuario especificado.
     * 
     * @param \InstagramAPI\Instagram $instagram Objeto de acceso a la API de Instagram
     * @param string $uid Id del usuario de Instagram al que se enviara el mensaje
     * @param string $message Texto del mensaje
     * @param int $count Cantidad de veces que se enviara el mensaje.
     * Si no se especifica, se asume que es uno.
     */
    private function sendMessage($instagram, $uid, $message, $count = 1)
    {
        $date = $this->getLocalDate();
        try {
            $msg = "Envio %d de %d...";
            $u = $this->getUserName($instagram, $uid);
            echo "$date -- Enviando mensaje a $u ($uid): \"$message\"\n";
            for($i = 0; $i < $count; $i++) {
                $g = $this->d_guid();
                $m = "$date -- $g / " . sprintf($msg, $i + 1, $count);
                $instagram->directMessage($uid, $message);
                echo "$m\n";
            }
            echo "$date -- Mensaje enviado a $u ($uid)\n";
            return;
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to send the message to $recip: $m\n";
            exit(0);
        }
    }

    /**
     * Devuelve el nombre de un usuario de Instagram dado su id.
     * 
     * @param \InstagramAPI\Instagram $instagram Instancia del objeto de acceso a Instagram
     * @param string $uid Id de Instagram del usuario
     */
    private function getUserName($instagram, $uid)
    {
        $date = $this->getLocalDate();
        try {
            $u = $instagram->getUserInfoById($uid)->user->username;
            echo "Obtenido nombre del usuario con id $uid: $u\n";
            return $u;
        } catch (Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get the username of user with id $uid: $m\n";
            exit(0);
        }
    }

    /**
     * Devuelve el id de un usuario de Instagram dado su nombre.
     * 
     * @param \InstagramAPI\Instagram $instagram Instancia del objeto de acceso a Instagram
     * @param string $name Id de Instagram del usuario
     */
    private function getUserId($instagram, $name)
    {
        $date = $this->getLocalDate();
        try {
            $uid = $instagram->getUsernameId($name);
            echo "Obtenido id del usuario $name: $uid\n";
            return $uid;
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get the username id of $name: $m\n";
            exit(0);
        }

    }

    /**
     * Inicia sesion en Instagram para el usuario dado.
     * 
     * @param \InstagramAPI\Instagram $instagram Instancia del objeto de acceso a Instagram
     * @param string $username Nombre del usuario que iniciara sesion
     * @param string $password Contraseña del usuario
     */
    private function loginToInstagram(&$instagram, $username, $password)
    {
        $date = $this->getLocalDate();
        try {
            echo "Iniciando sesión para el usuario: $username...\n";
            $instagram->setUser($username, $password);
            $instagram->login();
            echo "Sesión iniciada para el usuario: $username\n";
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to login: $m\n";
            exit(0);
        }
    }

    /**
     * Establece el proxy para acceder a Instagram.
     * 
     * @param \InstagramAPI\Instagram $instagram Instancia del objeto de acceso a Instagram
     * @param string $uid Direccion IP:Puerto del proxy
     */
    private function setClientProxy(&$instagram, $netProxy)
    {
        echo "Usando proxy: $netProxy\n";
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
     * Crea directorio donde se almacenan los directs
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
            exit(0);
        }

        try {
            mkdir($dir . '/queued');
            echo "Creado directorio de los directs que estan activos.\n";
        } catch (Exception $e) {
            $m = $e->getTraceAsString();
            echo "No se pudo crear directorio de los directs activos: $m\n";
            exit(0);
        }
        
        try {
            mkdir($dir . '/old');
            echo "Creado directorio de los directs que ya no estan activos.\n";
        } catch (Exception $e) {
            $m = $e->getTraceAsString();
            echo "No se pudo crear directorio de los directs enviados: $m\n";
            exit(0);
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
            echo "Obtenidas las credenciales para el usuario: $username\n";
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get Instagram credentials: $m\n";
            exit(0);
        }

    }
    
    /**
     * Carga desde un archivo los datos del proxy para acceder a Instagram.
     * 
     * @param string $netProxy Variable donde se guardaran los datos del proxy
     */
    private function getProxy(&$netProxy)
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
