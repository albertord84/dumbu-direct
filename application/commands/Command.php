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
        printf("- Obtenida instancia del objeto Instagram\n");
    }
    
    public function alreadyTexted($pk, $msg_id)
    {
        $db = self::$schema;
        $count = $db::table('stat')
                ->where('follower_id', $pk)
                ->where('msg_id', $msg_id)
                ->count();
        return $count > 0 ? TRUE : FALSE;
    }
    
    public function promoRecipients($msg_id)
    {
        $message = $this->getMessage($msg_id);
        $pk = $this->getUser($message->user_id)->pk;
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        $c = mt_rand(1, 5);
        $followers = trim(shell_exec("head -n $c $followers_file"));
        if ($followers == '') { return NULL; }
        $promo_recip = explode(PHP_EOL, $followers);
        printf("- Se enviara promocion a seguidores: [%s]\n", implode(',', $promo_recip));
        return $promo_recip;
    }
    
    public function promoRecipientsCount($pk)
    {
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        $count = trim(shell_exec("cat $followers_file | wc -l"));
        printf("- La promocion tiene %s seguidores pendientes...\n",
            $count);
        return intval($count);
    }
    
    public function promoDefinedFollowersList($pk)
    {
        $exists_followers_file = file_exists(FOLLOWERS_LIST_DIR . "/$pk.txt");
        return $exists_followers_file;
    }
    
    public function purgePromoRecipientsList($msg_id, &$followers)
    {
        $count = count($followers);
        for ($i = 0; $i < $count; $i++) {
            $already_texted = $this->alreadyTexted($followers[$i], $msg_id);
            if ($already_texted) {
                printf("- Sacando de la lista al seguidor %s porque ya recibio promocion\n",
                        $followers[$i]);
                array_splice($followers, $i, 1);
                $count--;
            }
        }
    }
    
    /**
     * 
     * @param array() $followers Arreglo con la lista de pks de los seguidores
     * a los que se les envio el mensaje.
     */
    public function insertStat($msg_id, $followers)
    {
        $message = $this->getMessage($msg_id);
        foreach ($followers as $follower) {
            if (trim($follower)=='') { continue; }
            if ($this->alreadyTexted($follower, $msg_id)) { continue; }
            $data = [
                'user_id' => $message->user_id,
                'follower_id' => $follower,
                'msg_id' => $message->id,
                'dt' => \Carbon\Carbon::now()->getTimestamp()
            ];
            $db = self::$schema;
            $db::table('stat')->insert($data);
        }
    }
    
    public function popAlreadyTexted($pk, $followers)
    {
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        foreach ($followers as $follower) {
            $cmd = "sed -i '/$follower/d' " . $followers_file;
            shell_exec($cmd);
        }
        printf("- Sacados de la lista de destinatarios los perfiles [%s]\n",
            implode(',', $followers));
    }

    public function randomGreeting($lang = 'pt')
    {
        $greetings = [ 
            'pt' => [
                0 => "Oi, todo bem?",
                1 => "Olá, todo bem?",
                2 => "Oi, como vai?",
                3 => "Como vai tudo?",
                4 => "Eu tenho algo para lhe dizer",
                5 => "Olá, como vai?",
            ],
            'en' => [
                0 => "Hi!",
                1 => "How are you?",
                2 => "How do you feel today? I want to tell you something...",
                3 => "I have something to tell you...",
            ]
        ];
        $n = mt_rand(0, count($greetings[$lang]) - 1);
        $greeting = $greetings[$lang][$n];
        printf("- Saludo aleatorio escogido: \"%s\"\n", $greeting);
        return $greeting;
    }
    
    public function sendGreeting($followers)
    {
        try {
            $greeting = $this->randomGreeting();
            $this->instagram->directMessage($followers, $greeting);
            printf("- Enviado saludo \"%s\" a los seguidores escogidos\n", $greeting);
        }
        catch (Exception $ex) {
            $msg = sprintf("- Error al enviar el saludo inicial; ERROR: \"%s\"\n",
                $ex->getMessage());
            throw new Exception($msg);
        }
    }
    
    public function sendMessage($msg_id, $followers)
    {
        $message = $this->getMessage($msg_id);
        try {
            $this->instagram->directMessage($followers, $message->msg_text);
            printf("- Se envio el mensaje \"%s...\"; a los seguidores [%s]\n",
                trim(substr($message->msg_text, 0, 15)), implode(", ", $followers));
        }
        catch (Exception $ex) {
            $msg = sprintf("- Error al enviar el mensaje \"%s...\"; ERROR: \"%s\"\n",
                trim(substr($message->msg_text, 0, 15)), $ex->getMessage());
            $this->setMessageFailed($msg_id, 1);
            $this->setMessageProcessing($msg_id, 0);
            throw new Exception($msg, 500);
        }
    }
    
    public function setMessageFailed($msg_id, $failed = 0)
    {
        $db = self::$schema;
        $db::table('message')
                ->where('id', $msg_id)
                ->update(['failed' => $failed]);
        printf("- Se establecio el estado del mensaje a \"%s\"...\n",
                $sent == 0 ? 'NO FALLIDO' : 'FALLIDO');
    }
    
    public function setMessageSent($msg_id, $sent = 0)
    {
        $db = self::$schema;
        $db::table('message')
                ->where('id', $msg_id)
                ->update(['sent' => $sent]);
        printf("- Se establecio el estado del mensaje a \"%s\"...\n",
                $sent == 0 ? 'NO ENVIADO' : 'ENVIADO');
    }
    
    public function setMessageProcessing($msg_id, $processing = 0)
    {
        $db = self::$schema;
        $db::table('message')
                ->where('id', $msg_id)
                ->update(['processing' => $processing]);
        printf("- Se establecio el estado del mensaje a \"%s\"...\n",
                $processing == 0 ? 'PROCESADO' : 'PROCESANDO');
    }
    
    public function isOldMsg($msg_id, $minutes = 10)
    {
        $before = \Carbon\Carbon::now()->subMinutes($minutes)->timestamp;
        $db = self::$schema;
        $sent_at = $db::table('message')->where('id', $msg_id)->value('sent_at');
        if ($sent_at <= $before) {
            return TRUE;
        }
        else {
            FALSE;
        }
    }
    
    public function getMessage($msg_id)
    {
        $db = self::$schema;
        $message = $db::table('message')->where('id', $msg_id)->first();
        return $message;
    }
    
    public function getUser($user_id)
    {
        $db = self::$schema;
        $user = $db::table('client')->where('id', $user_id)->first();
        printf("- Obtenidos datos del usuario %s\n", $user->username);
        return $user;
    }

    public function loginInstagram($username, $password) {
        try {
            $this->instagram->setUser($username, $password);
            $this->instagram->login();
            printf("- Iniciada sesión en Instagram como %s\n", $username);
        } catch (Exception $ex) {
            $msg = sprintf("- No se pudo iniciar sesion para \"%s\". CAUSA: \"%s\"\n", $username, $ex->getMessage());
            throw new Exception($msg);
        }
    }

    public function randomWait() {
        $secs = mt_rand(10, 30);
        printf("- Esperando %s segs para continuar\n", $secs);
        sleep($secs);
    }
    
    public function lockMessage() {
        file_put_contents(ROOT_DIR . '/var/message.lock', '');
        printf("- Bloqueada la cola de mensajes...\n");
    }
    
    public function unlockMessage() {
        if (file_exists(ROOT_DIR . '/var/message.lock')) {
            unlink(ROOT_DIR . '/var/message.lock');
            printf("- Desbloqueada la cola de mensajes...\n");
            return;
        }
        printf("- La cola de mensajes no estaba bloqueada...\n");
    }
    
    public function interrupt($msg = '')
    {
        if ($msg === '') {
            printf("INTERRUMPIDO!!!\n");
        }
        else {
            printf("INTERRUMPIDO!!! CAUSA: %s\n", $msg);
        }
        die();
    }
    
    public function messagesLocked()
    {
        $is_locked = file_exists(ROOT_DIR . '/var/message.lock');
        printf("- La cola de mensajes esta %s\n", $is_locked ? 'BLOQUEADA' : 'LIBERADA');
        return $is_locked;
    }
    
    public function oldestPromoList($minutes = 10, $count = 5)
    {
        $before = \Carbon\Carbon::now()->subMinutes($minutes)->timestamp;
        $db = self::$schema;
        $messages = $db::table('message')
                ->where('promo', 1)
                ->where('processing', 0)
                ->where('failed', 0)
                ->where('sent', 0)
                ->where('sent_at', '<=', $before)
                ->take($count)
                ->get();
        printf("- Devolviendo lista de las %s promociones mas antiguas...\n", $count);
        return $messages;
    }
    
    public function lastMessages($promo = FALSE)
    {
        $db = self::$schema;
        $messages = $db::table('message')
                ->where('processing', 0)
                ->where('failed', 0)
                ->where('sent', 0);
        
        if ($promo) {
            return $this->oldestPromoList();
        }
        else {
            printf("- Devolviendo lista de los ultimos 5 mensajes...\n");
            return $messages->where('promo', 0)->take(5)->get();
        }
    }
    
    public function dayStart()
    {
        return \Carbon\Carbon::parse(date('Y-m-d') . ' 00:00:00')->timestamp;
    }
    
    public function dailyLimitPassed($user_id, $limit = 200)
    {
        $db = self::$schema;
        $_limit = $db::table('stat')
                ->where('user_id', $user_id)
                ->where('dt', '>=', $this->dayStart())
                ->count();
        return $_limit < $limit ? FALSE : TRUE;
    }

}
