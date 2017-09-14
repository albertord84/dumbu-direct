<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DirectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages';
    
    protected $pk = '';

    protected $username = '';
    
    protected $password = '';
    
    protected $instagram = NULL;

    protected $proxy = NULL;

    protected $suspended = FALSE;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('America/Sao_Paulo');
        require_once APPPATH.'/../../vendor/autoload.php';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);

        if ($this->suspended) {
            echo sprintf("%s - Tarea %s suspendida por ahora" . PHP_EOL,
                date('r'), $this->signature);
            return;
        }
        
        echo sprintf("%s - Procesando mensajes de %s" . PHP_EOL,
                date('r'), $this->username);
        
        $this->loginInstagram();
        $this->processFirstTenMessages();
        
        echo sprintf("%s - Terminado el procesamiento de los mensajes de %s" . 
                PHP_EOL, date('r'), $this->username);
    }

    protected function currentHour()
    {
        return intval(date('H'));
    }
    
    protected function currentMinute()
    {
        return intval(date('i'));
    }
    
    protected function loginInstagram()
    {
        try {
            $this->instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
            $this->instagram->setUser($this->username, $this->password);
            if ($this->proxy !== NULL) {
                $this->instagram->setProxy($this->proxy);
            }
            $this->instagram->login();
        }
        catch(\Exception $e) {
            echo sprintf("%s - Error al iniciar sesion como %s: %s" . PHP_EOL,
                date('r'), $this->username, $e->getMessage());
        }
    }
    
    protected function checkAlreadyTexted($pk)
    {
        $cmd_output = sprintf('ls %s | grep -c %s', OLD_QUEUE_PATH, $pk);
        if (intval($cmd_output) === 1) {
            echo sprintf("%s - El cliente %s ya ha recibido mensajes..." . PHP_EOL,
                date('r'), $pk);
            return TRUE;
        }
        else FALSE;
    }
    
    protected function sendMessage($destProfileId, $message)
    {
        try {
            $this->instagram->directMessage($destProfileId, $message);
            echo sprintf("%s - Enviado mensaje: \"%s...\" al perfil %s" . PHP_EOL,
                date('r'), substr($message, 0, 20), $destProfileId);
            return TRUE;
        }
        catch (\Exception $e) {
            echo sprintf("%s - Error al enviar el mensaje a %s: %s" . PHP_EOL,
                date('r'), $destProfileId, $e->getMessage());
            //$this->changeThrottle();
            return TRUE;
        }
    }

    protected function processFirstTenMessages()
    {
        $firstTenFileNames = $this->getFirstTenFileNames();
        if (count($firstTenFileNames)==0) {
            echo sprintf("%s - Por ahora no hay mensajes para %s" . PHP_EOL,
                date('r'), $this->username);
            exit(0);
        }
        foreach ($firstTenFileNames as $fileName) {
            if ( !file_exists($fileName) ) { continue; }
            if ( strstr($fileName, '.json') == FALSE ) { continue; }
            echo sprintf("%s - Procesando mensaje %s" . PHP_EOL,
                date('r'), basename($fileName));
            $fileObj = json_decode( file_get_contents($fileName) );
            if ($this->checkAlreadyTexted($fileObj->pks[0])) {
                $this->popMessage($fileName);
                continue;
            }
            $resp = $this->sendMessage($fileObj->pks[0], $fileObj->message);
            $this->popMessage($fileName);
            sleep(5);
        }
    }
    
    protected function popMessage($fileName)
    {
        copy($fileName, OLD_QUEUE_PATH . basename($fileName));
        unlink($fileName);
        echo sprintf("%s - Sacado de la cola el mensaje %s" . PHP_EOL,
            date('r'), basename($fileName));
    }
    
    protected function guid() {
        $one = mt_rand(0, 65535);
        $two = mt_rand(0, 65535);
        $three = mt_rand(0, 65535);
        $four = mt_rand(0, 65535);
        $five = mt_rand(0, 65535);
        $guid = sprintf('%04X%04X%04X%04X%04X', $one, $two, $three, $four, $five);
        return strtolower($guid);
    }

    protected function getFirstTenFileNames()
    {
        $list = '/tmp/' . $this->guid();
        $cmd = sprintf('ls %s | grep %s > %s && head %s && rm %s', QUEUE_PATH, 
                $this->pk, $list, $list, $list);
        $cmd_output = shell_exec($cmd);
        $firstTenFileNames = explode( PHP_EOL, trim( $cmd_output ) );
        for ($i = 0; $i < count($firstTenFileNames); $i++)
        {
            $f = $firstTenFileNames[ $i ];
            $firstTenFileNames[ $i ] = QUEUE_PATH . $f;
        }
        return $firstTenFileNames;
    }
    
    protected function getProxiesList()
    {
        $cmd = "grep -v '#' " . APPPATH . '/config/net_proxy';
        $proxies = explode(PHP_EOL, trim(shell_exec($cmd)));
        return $proxies;
    }
    
    protected function setProxyNumber($proxyNumber)
    {
        $proxies = $this->getProxiesList();
        $this->proxy = $proxies[$proxyNumber];
        echo sprintf("%s - Usando proxy %s..." . PHP_EOL,
                date('r'), $this->proxy);
    }

}
