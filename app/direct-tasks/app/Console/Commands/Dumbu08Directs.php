<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Dumbu08Directs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendirects:dumbu08';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Instagram direct messages to dumbu.08 followers';
    
    // 4542814483 = dumbu.09
    // 4492293740 = dumbu.08
    private $pk = '4492293740';

    private $username = 'dumbu.08';
    
    private $password = 'Sorvete69';
    
    private $instagram = NULL;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
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
        
        echo sprintf("%s - Procesando mensajes de %s" . PHP_EOL,
                date('r'), $this->username);
        
        $this->loginInstagram();
        $this->processFirstTenMessages();
        
        echo sprintf("%s - Terminado el procesamiento de los mensajes de %s" . 
                PHP_EOL, date('r'), $this->username);
    }
    
    private function loginInstagram()
    {
        try {
            $this->instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
            $this->instagram->setUser($this->username, $this->password);
            $this->instagram->login();
        }
        catch(Exception $e) {
            echo sprintf("%s - Error al iniciar sesion como %s: %s" . PHP_EOL,
                date('r'), $this->username, $e->getMessage());
        }
    }
    
    private function processFirstTenMessages()
    {
        $firstTenFileNames = $this->getFirstTenFileNames();
        if (count($firstTenFileNames)==0) {
            echo sprintf("%s - Por ahora no hay mensajes para %s" . PHP_EOL,
                date('r'), $this->username);
            exit(0);
        }
        foreach ($firstTenFileNames as $fileName) {
            if ( !file_exists($fileName) ) { continue; }
            echo sprintf("%s - Procesando mensaje %s" . PHP_EOL,
                date('r'), $fileName);
            $fileObj = json_decode( file_get_contents($fileName) );
            try {
                $this->instagram->directMessage($fileObj->pks[0], $fileObj->message);
                echo sprintf("%s - Enviado mensaje %s al perfil %s" . PHP_EOL,
                    date('r'), $fileName, $fileObj->pks[0]);
                $this->popMessage($fileName);
            }
            catch (Exception $e) {
                echo sprintf("%s - Error al enviar el mensaje a %s: %s" . PHP_EOL,
                    date('r'), $fileObj->pks[0], $e->getMessage());
                //$this->changeThrottle();
                $this->popMessage($fileName);
            }
        }
    }
    
    private function popMessage($fileName)
    {
        copy($fileName, __DIR__ . '/../../../../application/logs/directs/old' .
            basename($fileName));
        unlink($fileName);
        echo sprintf("%s - Sacado de la cola el mensaje %s" . PHP_EOL,
            date('r'), $fileName);
    }


    private function getFirstTenFileNames()
    {
        $cmd = sprintf('ls %s | grep %s | head', 
                __DIR__ . '/../../../../application/logs/directs/queue',
                $this->pk);
        echo $cmd . PHP_EOL;
        $cmd_output = shell_exec($cmd);
        $firstTenFileNames = explode( PHP_EOL, trim( $cmd_output ) );
        return $firstTenFileNames;
    }

}
