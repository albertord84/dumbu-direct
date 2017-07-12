<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Valores necesarios para usar el gestor de la cola de mensajes
define('BASEPATH', __DIR__ . '/../../../../system');
define('APPPATH', __DIR__ . '/../../../../application');

include_once APPPATH . '/libraries/directs/queue/Manager.php';

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
    
    private $username = NULL;
    
    private $password = NULL;
    
    private $net_proxy = NULL;
    
    private $instagram = NULL;
    
    private $qManager = NULL;

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
        set_time_limit(0);
        
        echo sprintf("%s - Comenzando procesamiento de la cola de mensajes..." . 
            PHP_EOL, $this->getLocalDate());
        
        $qm = new \Manager();
        if ($qm->queue_count()==0) {
            echo 'No hay mensajes que procesar por ahora.' . PHP_EOL;
            return;
        }

        // Quitar cuando se vaya a probar el procesamiento de la cola
        //if (TRUE) return;

        $this->handleDirectsStore();
        $this->getAutoloader();
        
        // Estas credenciales para hacer el envio, se rotaran cuando
        // la API haya dado un error de congestion o de chequeo de
        // identidad.
        $this->getInstagCreds();
        
        $this->getProxy();
        $this->instagram = $this->getInstagram(FALSE, FALSE);
        
        if ($this->net_proxy) {
            $this->setClientProxy($this->net_proxy);
        }
        
        $this->loginToInstagram();
        
        //$this->sendMessage($this->getUserId('dumbu.08'), "Probando codigo luego de pasar la escoba");
        $this->processQueue();
        
        $this->logoutInstagram();
        
        echo sprintf("%s - Terminado el procesamiento de la cola de mensajes." . 
            PHP_EOL, $this->getLocalDate());
    }
    
    private function processQueue()
    {
        $page_size = 1;
        $this->qManager = new \Manager();
        
        if ($this->qManager->queue_count()==0) return;
        
        $page = 1;
        $last_msg = NULL;
        $msg_list = NULL;
        
        $last = $this->qManager->last_sent();
        
        if ( ! $last ) {
            echo 'Comenzar desde el comienzo de la cola...' . PHP_EOL;
            $msg_list = $this->qManager->msg_page($page, $page_size);
            $last_msg = $msg_list[ $page_size - 1 ];
        }
        else {
            echo 'Comenzar donde quedo el puntero de cola...' . PHP_EOL;
            $json_obj = json_decode( $this->qManager->last_sent() );
            $page = $json_obj->page;
            $msg_list = $this->qManager->msg_page($page, $page_size);
            $last_msg = $msg_list[ $page_size - 1 ];
        }
        
        for ($i = 0; $i < count($msg_list); $i++) {
            $msg_file = $this->getDirStore() . "/queue/"
                    . $msg_list[ $i ];
            echo sprintf('Procesando mensaje con id %s...' . PHP_EOL, 
                    basename($msg_file));
            $msg = json_decode( file_get_contents($msg_file) );
            $text = $msg->message;
            $recipients = $this->cleanPks($msg->pks);
            try {
                $this->writeTo($recipients, $text);
            }
            catch (\Exception $e)
            {
                echo sprintf('Error al enviar mensaje %s: %s (linea %s)' . PHP_EOL,
                        $msg->datetime, $e->getTraceAsString(), $e->getLine());
                echo sprintf('Aplazando envio de mensajes a partir de %s_%s' . PHP_EOL,
                        $msg->datetime, $msg->uid);
                return;
            }
        }
        $this->qManager->set_last($last_msg, $page);
        
    }
    
    /**
     * Recorre la lista de perfiles enviandole a cada uno el mensaje especificado
     * 
     * @param array $pks Lista de perfiles
     * @param string $msg Texto del mensaje que se enviara
     */
    private function writeTo($pks, $msg)
    {
        echo sprintf('Enviando mensaje a %s perfile(s)...' . PHP_EOL,
                count($pks));
        for ($i = 0; $i < count($pks); $i++) {
            $pk = $pks[ $i ];
            $this->sendMessage($pk, $msg);
        }
    }


    /**
     * Determina si alguno de los perfiles ya esta recibiendo mensajes.
     * 
     * @param array $pks Arreglo con la lista de perfiles a los que se escribira
     * @return array Nueva lista sin los perfiles a los que ya se esta escribiendo
     */
    private function cleanPks($pks)
    {
        echo sprintf('Obviando perfiles con mensajes ya en cola...' . PHP_EOL);
        $new_list = [];
        for ($i = 0; $i < count($pks); $i++) {
            $pk = $pks[ $i ];
            $pk_taken = $this->qManager->pk_taken( $pk );
            if ( $pk_taken ) {
                echo sprintf('Obviando el perfil con id %s...' . 
                    PHP_EOL, $pk);
            }
            else {
                echo sprintf('Manteniendo perfil %s como destinatario...' . PHP_EOL,
                        $pk);
                $new_list[] = $pk;
            }
        }
        return $new_list;
    }

    /**
     * Chequea si existe el estanque donde se echaran los mensajes.
     * Si no existe manda a crearlo.
     */
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
     */
    private function logoutInstagram()
    {
        $date = $this->getLocalDate();
        try {
            $u = $this->instagram->getSelfUserInfo()->user->username;
            echo "Cerrando sesión para el usuario: $u...\n";
            $this->instagram->logout();
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
     * @param string $uid Id del usuario de Instagram al que se enviara el mensaje
     * @param string $message Texto del mensaje
     * @param int $count Cantidad de veces que se enviara el mensaje.
     * Si no se especifica, se asume que es uno.
     */
    private function sendMessage($uid, $message, $count = 1)
    {
        $date = $this->getLocalDate();
        try {
            $msg = "Envio %d de %d...";
            //$u = $this->getUserName($uid);
            echo "$date -- Enviando mensaje al perfil $uid: \"$message\"\n";
            for($i = 0; $i < $count; $i++) {
                $g = $this->d_guid();
                $m = "$date -- $g / " . sprintf($msg, $i + 1, $count);
                $this->instagram->directMessage($uid, $message);
                echo "$m\n";
            }
            echo "$date -- Mensaje enviado al perfil $uid\n";
            return;
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to send the message to profile $uid: $m\n";
            exit(0);
        }
    }

    /**
     * Devuelve el nombre de un usuario de Instagram dado su id.
     * 
     * @param string $uid Id de Instagram del usuario
     */
    private function getUserName($uid)
    {
        $date = $this->getLocalDate();
        try {
            $u = $this->instagram->getUserInfoById($uid)->user->username;
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
     * @param string $name Nombre del usuario en Instagram
     */
    private function getUserId($name)
    {
        $date = $this->getLocalDate();
        try {
            $uid = $this->instagram->getUsernameId($name);
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
     */
    private function loginToInstagram()
    {
        $date = $this->getLocalDate();
        try {
            echo "Iniciando sesión para el usuario: $this->username...\n";
            $this->instagram->setUser($this->username, $this->password);
            $this->instagram->login();
            echo sprintf("Sesión iniciada para el usuario: %s\n",
                $this->username);
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
    private function setClientProxy($net_proxy)
    {
        echo "Usando proxy: $net_proxy\n";
        $this->instagram->client->setProxy($net_proxy);
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
     * @return string Directorio de almacenar los directs o FALSE
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
            mkdir($dir . '/queue');
            echo "Creado directorio de los directs que estan activos.\n";
        } catch (Exception $e) {
            $m = $e->getTraceAsString();
            echo "No se pudo crear directorio de los directs activos: $m\n";
            exit(0);
        }
        
        try {
            mkdir($dir . '/queue/error');
            echo "Creado directorio de los directs que dan error de envio.\n";
        } catch (Exception $e) {
            $m = $e->getTraceAsString();
            echo "No se pudo crear directorio de los directs erroneos: $m\n";
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
     * en las propiedades correspondientes.
     */
    private function getInstagCreds()
    {
        $date = $this->getLocalDate();
        try {
            $instag_creds_file = __DIR__.'/../../../../../web/instagram_credentials';
            $_creds = file_get_contents($instag_creds_file);
            $creds = explode(':', $_creds);
            $this->username = $creds[0];
            $this->password = $creds[1];
            echo sprintf('Obtenidas las credenciales para el usuario: %s' . PHP_EOL,
                    $this->username);
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get Instagram credentials: $m\n";
            exit(0);
        }

    }
    
    /**
     * Carga desde un archivo los datos del proxy para acceder a Instagram.
     */
    private function getProxy()
    {
        $date = $this->getLocalDate();
        try {
            $proxy_data_file = __DIR__.'/../../../../../web/net_proxy';
            if (file_exists($proxy_data_file)) {
                $this->net_proxy = file_get_contents($proxy_data_file);
            }
            if (empty($this->net_proxy) || trim($this->net_proxy)=='') {
                $this->net_proxy = FALSE;
            }
        } catch (\Exception $e) {
            $m = $e->getMessage();
            echo "$date -- Something went wrong trying to get the network proxy data: $m\n";
            exit(0);
        }
    }

    /**
     * Devuelve instancia de \InstagramAPI\Instagram
     */
    private function getInstagram($debug, $truncatedDebug)
    {
        return new \InstagramAPI\Instagram($debug, $truncatedDebug);
    }

    /**
     * Devuelve una cadena con la fecha/hora en formato YYYY-MM-DD hh:mm:ss
     */
    private function getLocalDate()
    {
        $date_cmd = `date "+%F %r"`;
        $date = trim($date_cmd);
        return $date;
    }
}
