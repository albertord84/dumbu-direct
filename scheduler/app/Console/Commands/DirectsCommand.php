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
    
    protected $last_action = 0;

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
        if (!$this->hasPendingTasks()) {
            $this->log(sprintf("No hay envios pendientes por ahora para %s", $this->signature));
            return;
        }
        
        if ($this->suspended) {
            $this->log(sprintf("Tarea %s suspendida por ahora", $this->signature));
            return;
        }
        
        $this->log(sprintf("Procesando mensajes de %s", $this->username));
        
        $this->loginInstagram();
        $this->processFirstTenMessages();
        
        $this->log(sprintf("Terminado el procesamiento de los mensajes de %s",
                $this->username));
    }
    
    protected function log($t)
    {
        echo sprintf("%s - %s" . PHP_EOL, date('r'), $t);
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
            $this->log(sprintf("Error al iniciar sesion como %s: %s",
                $this->username, $e->getMessage()));
        }
    }
    
    protected function hasPendingTasks()
    {
        $cmd = sprintf("ls %s | grep -c %s",
                ROOT_DIR . '/var/queue',
                $this->pk);
        $cmd_out = trim(shell_exec($cmd));
        return intval($cmd_out) > 0;
    }

    protected function checkAlreadyTexted($pk)
    {
        $cmd = sprintf('grep -c %s %s', $pk, 
                ROOT_DIR . '/var/old/already.texted.list');
        $cmd_output = trim(shell_exec($cmd));
        if (intval($cmd_output) === 1) {
            $this->log(sprintf("El cliente %s ya ha recibido mensajes...", $pk));
            return TRUE;
        }
        else FALSE;
    }
    
    protected function setUserAsTexted($pk)
    {
        $cmd = sprintf("echo %s >> %s", $pk,
                ROOT_DIR . '/var/old/already.texted.list');
        shell_exec($cmd);
    }
    
    protected function simulateHumanInteraction()
    {
        $this->instagram->getAutoCompleteUserList();
        $this->instagram->getReelsTrayFeed();
        $this->instagram->getRankedRecipients();
        $this->instagram->getRecentRecipients();
        $this->instagram->getMegaphoneLog();
        $this->instagram->getRecentActivity();
        
        while ( ( $action = mt_rand(0, 2) ) === $this->last_action ) {}
        $this->last_action = $action;
        
        if ($action === 0) {
            $media_id = $this->instagram->getPopularFeed()->items[0]->id;
            $this->instagram->comment($media_id, 'Eu gosto!');
            sleep(5);
            return;
        }
        if ($action === 1) {
            $media_id = $this->instagram->getPopularFeed()->items[0]->id;
            $this->instagram->like($media_id);
            sleep(5);
            return;
        }
        if ($action === 2) {
            $media_id = $this->instagram->getPopularFeed()->items[0]->id;
            $this->instagram->getMediaComments($media_id);
            sleep(5);
            return;
        }
    }

    protected function sendMessage($destProfileId, $message)
    {
        try {
            $this->instagram->directMessage($destProfileId, $message);
            $this->log(sprintf("Usuario %s(%s) envio mensaje: \"%s...\" al perfil %s",
                $this->username, $this->pk, substr($message, 0, 20), $destProfileId));
            return TRUE;
        }
        catch (\Exception $e) {
            $this->log(sprintf("Error de envio %s(%s) => %s: %s",
                $this->username, $this->pk, $destProfileId, $e->getMessage()));
            return TRUE;
        }
    }

    protected function processFirstTenMessages()
    {
        $firstTenFileNames = $this->getFirstTenFileNames();
        if (count($firstTenFileNames)==0) {
            $this->log(sprintf("Por ahora no hay mensajes para %s", $this->username));
            return;
        }
        foreach ($firstTenFileNames as $fileName) {
            if ( !file_exists($fileName) ) { continue; }
            if ( strstr($fileName, '.json') == FALSE ) { continue; }
            $this->log(sprintf("Procesando mensaje %s", basename($fileName)));
            $fileObj = json_decode( file_get_contents($fileName) );
            if ($this->checkAlreadyTexted($fileObj->pks[0])) {
                $this->popMessage($fileName);
                continue;
            }
            $this->sendMessage($fileObj->pks[0], $fileObj->message);
            $this->simulateHumanInteraction();
            $this->popMessage($fileName);
            $this->setUserAsTexted($fileObj->pks[0]);
            $randomWait = mt_rand(30, 180);
            $this->log(sprintf("Esperando %s segundos para enviar el siguiente mensaje...",
                    $randomWait));
            sleep($randomWait);
        }
    }
    
    protected function popMessage($fileName)
    {
        unlink($fileName);
        $this->log(sprintf("Sacado de la cola el mensaje %s", basename($fileName)));
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
        $cmd = sprintf('ls %s | grep %s > %s && head %s && rm %s',
                QUEUE_PATH,
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
        $cmd = "grep -v '#' " . ETC_DIR . '/net_proxy';
        $proxies = explode(PHP_EOL, trim(shell_exec($cmd)));
        return $proxies;
    }
    
    protected function setProxyNumber($proxyNumber)
    {
        $proxies = $this->getProxiesList();
        $this->proxy = $proxies[$proxyNumber];
        $this->log(sprintf("Usando proxy %s para envios de %s...",
                $this->proxy, $this->username));
    }

}
