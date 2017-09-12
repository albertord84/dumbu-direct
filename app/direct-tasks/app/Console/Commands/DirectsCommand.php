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
            $resp = $this->sendMessage($fileObj->pks[0], $fileObj->message);
            $this->popMessage($fileName);
            sleep(5);
        }
    }
    
    protected function popMessage($fileName)
    {
        copy($fileName, __DIR__ . '/../../../../application/logs/directs/old/' .
            basename($fileName));
        unlink($fileName);
        echo sprintf("%s - Sacado de la cola el mensaje %s" . PHP_EOL,
            date('r'), basename($fileName));
    }

    protected function getFirstTenFileNames()
    {
        $cmd = sprintf('ls %s | grep %s | tail', 
                __DIR__ . '/../../../../application/logs/directs/queue',
                $this->pk);
        $cmd_output = shell_exec($cmd);
        $firstTenFileNames = explode( PHP_EOL, trim( $cmd_output ) );
        for ($i = 0; $i < count($firstTenFileNames); $i++)
        {
            $f = $firstTenFileNames[ $i ];
            $firstTenFileNames[ $i ] = __DIR__ . '/../../../../application/logs/directs/queue/' .
                    $f;
        }
        return $firstTenFileNames;
    }

}
