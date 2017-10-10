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
    
    public function alreadyTexted($pk, $msg_id)
    {
        $data = self::$schema->select('select id from stat where follower_id = ? and msg_id = ?', [
            $pk, $msg_id
        ]);
        return count($data) > 0 ? TRUE : FALSE;
    }
    
    public function messageRecipients($msg_id)
    {
        $data = self::$schema->select('select follower_id from stat where msg_id = ?', [
            $msg_id
        ]);
        return $data;
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
        printf("Se enviara promocion a seguidores: [%s]\n", implode(',', $promo_recip));
        return $promo_recip;
    }
    
    public function promoRecipientsCount($pk)
    {
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        $count = trim(shell_exec("cat $followers_file | wc -l"));
        return intval($count);
    }
    
    public function promoDefinedFollowersList($pk)
    {
        $exists_followers_file = file_exists(FOLLOWERS_LIST_DIR . "/$pk.txt");
        return $exists_followers_file;
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
            self::$schema->insert("insert into stat (user_id, follower_id, msg_id, dt) "
                    . "values (?, ?, ?, ?)",
                    $data);
        }
    }
    
    public function popAlreadyTexted($pk, $followers)
    {
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        foreach ($followers as $follower) {
            $cmd = "sed -i '/$follower/d' " . $followers_file;
            shell_exec($cmd);
        }
        printf("Sacados de la lista de destinatarios los perfiles [%s]\n",
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
        printf("Saludo aleatorio escogido: \"%s\"\n", $greeting);
        return $greeting;
    }
    
    public function sendMessage($msg_id, $followers)
    {
        $message = $this->getMessage($msg_id);
        try {
            $this->instagram->directMessage($followers, $message->msg_text);
        }
        catch (Exception $ex) {
            $msg = sprintf("Error al enviar el mensaje \"%s...\"; ERROR: \"%s\"\n",
                trim(substr($message->msg_text, 0, 15)), $ex->getMessage());
            $this->setMessageFailed($msg_id, 1);
            throw new Exception($msg, 500);
        }
    }
    
    public function setMessageFailed($msg_id, $failed = 0)
    {
        self::$schema->update('update message set failed = ? where id = ?', [
            $failed, $msg_id
        ]);
    }
    
    public function setMessageSent($msg_id, $sent = 0)
    {
        self::$schema->update('update message set sent = ? where id = ?', [
            $sent, $msg_id
        ]);
    }
    
    public function setMessageProcessing($msg_id, $processing = 0)
    {
        self::$schema->update('update message set processing = ? where id = ?', [
            $processing, $msg_id
        ]);
    }
    
    public function getMessage($msg_id)
    {
        $data = self::$schema->select('select * from message where id = ?', [
            $msg_id
        ]);
        if (count($data)==0) { return NULL; }
        return $data[0];
    }
    
    public function getUser($user_id)
    {
        $data = self::$schema->select('select * from client where id = ?', [
            $user_id
        ]);
        if (count($data)==0) { return NULL; }
        return $data[0];
    }

    public function loginInstagram($username, $password) {
        try {
            $this->instagram->setUser($username, $password);
            $this->instagram->login();
            printf("Iniciada sesión en Instagram como %s\n", $username);
        } catch (Exception $ex) {
            $msg = sprintf("No se pudo iniciar sesion para \"%s\". CAUSA: \"%s\"", $username, $ex->getMessage());
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
    
    public function dayStart()
    {
        return \Carbon\Carbon::parse(date('Y-m-d') . ' 00:00:00')->timestamp;
    }
    
    public function dailyLimitPassed($user_id, $limit = 200)
    {
        $data = self::$schema->select('select count(*) as sent from stat where user_id = ? and dt >= ?', [
            $user_id, $this->dayStart()
        ]);
        return $data[0]->sent < $limit ? TRUE : FALSE;
    }

}
