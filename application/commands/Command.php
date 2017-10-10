<?php

/**
 * 
 */
class Command {

    public static $schema = NULL;
    public $instagram = NULL;

    function __construct() {
    }
    
    protected function init()
    {
    }

    protected function now() {
        return \Carbon\Carbon::now()->format('d-M H:i:s');
    }

    public function getInstagram() {
        $this->instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
    }

    public function loginInstagram($user) {
        try {
            $this->instagram->setUser($user->username, $user->password);
            $this->instagram->login();
            printf("Iniciada sesión en Instagram como %s\n", $user->username);
        } catch (Exception $ex) {
            $msg = sprintf("No se pudo iniciar sesion para \"%s\". CAUSA: \"%s\"", $user->username, $ex->getMessage());
            throw new Exception($msg);
        }
    }

    public function randomWait() {
        $secs = mt_rand(10, 30);
        printf("Esperando %s segs para continuar\n", $secs);
        sleep($secs);
    }
    
    public function lockMessage() {
        file_put_contents(ROOT_DIR . '/var/message.lock', '');
    }
    
    public function unlockMessage() {
        unlink(ROOT_DIR . '/var/message.lock');
    }
    
    public function interrupt($msg = '')
    {
        if ($msg === '') {
            printf("Interrumpido!!!\n");
        }
        else {
            printf("Interrumpido. CAUSA: %s\n", $msg);
        }
        die();
    }
    
    public function messagesLocked()
    {
        return file_exists(ROOT_DIR . '/var/message.lock');
    }
    
    public function lastMessages($promo = FALSE)
    {
        $messages = self::$schema->table('message')
                ->where('processing', 0)
                ->where('failed', 0)
                ->where('sent', 0);
        
        if ($promo) {
            return $messages->where('promo', 1)->take(5)->get();
        }
        else {
            return $messages->where('promo', 0)->take(5)->get();
        }
    }

}
