<?php

class Scheduler extends CI_Controller {

    public $instagram = NULL;

    public function index() {
        set_time_limit(0);
        printf("\n%s - Procesando mensajes...\n", $this->now());
        try {
            $this->processMessages();
        } catch (Exception $ex) {
            show_error('Error al procesar los mensajes: ' .
                    $ex->getMessage(), 500);
            return;
        }
        printf("%s - Terminado el procesamiento de mensajes.\n", $this->now());
        return;
    }

    public function now()
    {
        date_default_timezone_set(TIME_ZONE);
        return trim(shell_exec("date \"+%d/%b %r\""));
    }

    public function getInstagram()
    {
        $this->instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
    }

    public function getMessage($msg_id)
    {
        $this->load->database();
        $this->db->where('id', $msg_id);
        $query = $this->db->get('message');
        $result = $query->result();
        if (count($result) == 1) {
            return $result[0];
        } else {
            throw new Exception('No se pudo obtener el mensaje con id ' .
                $msg_id, 500);
        }
    }

    public function loginInstagram($user)
    {
        try {
            $this->instagram->setUser($user->username, $user->password);
            $this->instagram->login();
            printf("Iniciada sesión en Instagram como %s\n", $user->username);
        } catch (Exception $ex) {
            throw new Exception('No se pudo iniciar sesión para ' .
                $user->username, 500);
        }
    }

    public function randomWait()
    {
        $secs = mt_rand(10, 30);
        printf("Esperando %s segs para enviar\n", $secs);
        sleep($secs);
    }

    public function setMessageFailed($msg_id)
    {
        $this->load->database();
        $this->db->where('id', $msg_id);
        $this->db->update('message', [ 'failed' => 1 ]);
        printf("El mensaje %s se establecio como FALLIDO\n", $msg_id);
    }

    public function sendMessage($msg_id)
    {
        $message = $this->getMessage($msg_id);
        $user = $this->getUser($message->user_id);
        $followers = $this->getMessageRecipients($msg_id);
        if (count($followers)==0) {
            printf("El mensaje \"%s...\" ya se envio antes a los seguidores seleccionados\n",
                trim(substr($message->msg_text, 0, 15)));
            return;
        }
        $this->loginInstagram($user);
        try {
            $this->instagram->directMessage($followers, $message->msg_text);
            printf("Enviado mensaje: \"%s...\"; a los seguidores [%s]\n",
                trim(substr($message->msg_text, 0, 15)), implode(',', $followers));
        }
        catch (Exception $ex) {
            $msg = sprintf("Error al enviar el mensaje \"%s...\"; ERROR: \"%s\"\n",
                trim(substr($message->msg_text, 0, 15)), $ex->getMessage());
            $this->setMessageFailed($msg_id);
            throw new Exception($msg, 500);
        }
    }

    public function cleanInstagramApiSession($username)
    {
        $dir = INSTAGRAM_SESSIONS . '/' . $username;
        if (file_exists($dir)) {
            shell_exec("rm -r " . $dir);
        }
    }

    public function getMessageRecipients($msg_id)
    {
        $this->load->database();
        $this->db->where('msg_id', $msg_id);
        $query = $this->db->get('stat');
        $result = $query->result();
        if (count($result) > 0) {
            $follower_ids = [];
            foreach ($result as $r) {
                $follower_ids[] = $r->follower_id;
            }
            return $follower_ids;
        } else {
            return NULL;
        }
    }

    public function processMessages()
    {
        if(file_exists(ROOT_DIR . '/var/message.lock')) {
            printf("Esta bloqueado el acceso a la cola de mensajes\n");
            return;
        }
        $this->lockMessage();
        $message_ids = $this->lastMessageIds();
        if ($message_ids == NULL) {
            printf("No hay mensajes en cola por ahora\n");
            $this->unlockMessage();
            return;
        }
        foreach ($message_ids as $msg_id) {
            $this->getInstagram();
            $this->setMessageProcessing($msg_id, 1);
            $this->randomWait();
            $this->sendMessage($msg_id);
            $this->setMessageSent($msg_id);
            $this->setMessageProcessing($msg_id, 0);
        }
        $this->unlockMessage();
    }

    public function processSpecialMessages()
    {
        if(file_exists(ROOT_DIR . '/var/message.lock')) {
            printf("Esta bloqueado el acceso a la cola de promociones\n");
            return;
        }
        $this->lockMessage();
        $messages = $this->lastSpecialMessages();
        if ($messages == NULL) {
            printf("No hay mensajes especiales en cola por ahora\n");
            $this->unlockMessage();
            return;
        }
        foreach ($messages as $message) {
            $user_id = $message->user_id;
            $pk = $this->getUser($user_id)->pk;
            if (!$this->hasDefinedFollowers($pk)) {
                printf("No se ha definido lista de seguidores del mensaje: \"%s...\"\n",
                    trim(substr($message->msg_text, 0, 15)));
                continue;
            }
            $followers = $this->getSpecialMessageRecipients($message->id);
            if ($followers == NULL) {
                printf("Ya se envio este mensaje \"%s...\" a toda la lista de seguidores\n",
                    trim(substr($message->msg_text, 0, 15)));
                $this->setMessageSent($message->id);
                continue;
            }
            try {
                $this->getInstagram();
                $this->setMessageProcessing($message->id, 1);
                $this->sendSpecialMessage($message->id, $followers);
                $this->setMessageProcessing($message->id, 0);
                if ($this->getSpecialRecipientsCount($pk)===0) {
                    $this->setMessageSent($message->id);
                }
                $this->popAlreadyTexted($pk, $followers);
                $this->insertSpecialMessageStat($message->id, $followers);
            } catch (Exception $ex) {
                $this->setMessageProcessing($message->id, 0);
                $this->setMessageFailed($message->id);
                printf("No se pudo enviar el mensaje especial \"%s...\"\n",
                    trim(substr($message->msg_text, 0, 15)));
                printf("CAUSA: \"%s...\"\n", $ex->getMessage());
                continue;
            }
        }
        $this->unlockMessage();
    }

    public function getUser($user_id) {
        $this->load->database();
        $this->db->where('id', $user_id);
        $query = $this->db->get('client');
        $result = $query->result();
        if (count($result) == 1) {
            return $result[0];
        } else {
            throw new Exception('No se pudo obtener los datos del usuario con id ' .
                $message->user_id, 500);
        }
    }

    public function lockMessage() {
        write_file(ROOT_DIR . '/var/message.lock', '');
    }

    public function unlockMessage() {
        $lock_file = ROOT_DIR . '/var/message.lock';
        if (file_exists($lock_file)) {
            unlink($lock_file);
        }
    }

    public function lastMessageIds() {
        $this->load->database();
        $this->db->where('processing', 0);
        $this->db->where('sent', 0);
        $this->db->where('promo', 0);
        $this->db->limit(5);
        $query = $this->db->get('message');
        $messages = $query->result();
        $resp = [];
        if (count($messages) == 0) {
            return NULL;
        }
        foreach ($messages as $message) {
            $resp[] = $message->id;
        }
        return $resp;
    }

    public function setMessageProcessing($msg_id, $status)
    {
        $this->load->database();
        $this->db->where('id', $msg_id);
        $this->db->update('message', [ 'processing' => $status ]);
    }

    public function setMessageSent($msg_id)
    {
        $this->load->database();
        $this->db->where('id', $msg_id);
        $this->db->update('message', [
            'sent' => 1,
            'failed' => 0,
            'sent_at' => \Carbon\Carbon::now()->timestamp
        ]);
        printf("Se actualizo el mensaje con id %s a estado: ENVIADO\n", $msg_id);
    }

    public function lastSpecialMessages() {
        $this->load->database();
        $this->db->where('processing', 0);
        $this->db->where('failed', 0);
        $this->db->where('sent', 0);
        $this->db->where('promo', 1);
        $this->db->limit(5);
        $query = $this->db->get('message');
        $messages = $query->result();
        if (count($messages) == 0) {
            return NULL;
        }
        return $messages;
    }

    public function hasDefinedFollowers($pk)
    {
        $exists_followers_file = file_exists(FOLLOWERS_LIST_DIR . "/$pk.txt");
        return $exists_followers_file;
    }

    public function getSpecialMessageRecipients($msg_id)
    {
        $message = $this->getMessage($msg_id);
        $pk = $this->getUser($message->user_id)->pk;
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        $c = mt_rand(1, 5);
        $followers = trim(shell_exec("head -n $c $followers_file"));
        if ($followers == '') { return NULL; }
        $followers_array = explode(PHP_EOL, $followers);
        printf("Se enviara el mensaje a los seguidores: [%s]\n",
                implode(',', $followers_array));
        return $followers_array;
    }

    public function getSpecialRecipientsCount($pk)
    {
        $followers_file = FOLLOWERS_LIST_DIR . '/' . $pk . '.txt';
        $count = trim(shell_exec("cat $followers_file | wc -l"));
        return intval($count);
    }

    public function randomGreeting()
    {
        $greetings = [
            0 => "Oi, todo bem?",
            1 => "Olá, todo bem?",
            2 => "Oi, como vai?",
            3 => "Como vai tudo?",
            4 => "Eu tenho algo para lhe dizer",
            5 => "Olá, como vai?"
        ];
        $n = mt_rand(0, count($greetings) - 1);
        printf("Saludo aleatorio escogido: \"%s\"\n", $greetings[$n]);
        return $greetings[$n];
    }
    
    public function alreadyTextedWithSpecial($pk, $followers)
    {
        $_followers = [];
        foreach ($followers as $follower) {
            $this->load->database();
            $this->db->where('follower_id', $follower);
            $query = $this->db->get('stat');
            $stats = $query->result();
            if (count($stats) === 0) {
                $_followers[] = $follower;
            }
        }
        return count($_followers) > 0 ? $_followers : NULL;
    }

    public function sendSpecialMessage($msg_id, $followers)
    {
        $message = $this->getMessage($msg_id);
        $user = $this->getUser($message->user_id);
        $this->loginInstagram($user);
        try {
            $_followers = $this->alreadyTextedWithSpecial($user->pk, $followers);
            if ($_followers == NULL) {
                printf("Estos seguidores [%s] ya fueron texteados con promociones\n",
                    implode(',', $followers));
                return;
            }
            $this->randomWait();
            $this->instagram->directMessage($_followers, $this->randomGreeting());
            $this->randomWait();
            $this->instagram->directMessage($_followers, $message->msg_text);
            printf("Enviado mensaje: \"%s...\"; a los seguidores [%s]\n",
                trim(substr($message->msg_text, 0, 15)), implode(',', $followers));
        } catch (Exception $ex) {
            $msg = sprintf("Error al enviar el mensaje \"%s...\"; ERROR: \"%s\"\n",
                trim(substr($message->msg_text, 0, 15)), $ex->getMessage());
            throw new Exception($msg, 500);
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

    public function followerAlreadyStat($pk, $msg_id)
    {
        $this->load->database();
        $this->db->where('follower_id', $pk);
        $this->db->where('msg_id', $msg_id);
        $query = $this->db->get('stat');
        $result = $query->result();
        if (count($result) >= 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function insertSpecialMessageStat($msg_id, $followers)
    {
        $message = $this->getMessage($msg_id);
        $this->load->database();
        foreach ($followers as $follower) {
            if (trim($follower)=='') { continue; }
            if ($this->followerAlreadyStat($follower, $msg_id)) { continue; }
            $data = [
                'user_id' => $message->user_id,
                'follower_id' => $follower,
                'msg_id' => $message->id,
                'dt' => \Carbon\Carbon::now()->getTimestamp()
            ];
            $this->db->insert('stat', $data);
        }
    }

    public function special()
    {
        set_time_limit(0);
        printf("\n%s - Procesando mensajes especiales...\n", $this->now());
        try {
            $this->processSpecialMessages();
        } catch (Exception $ex) {
            show_error('Error al procesar los mensajes especiales: ' .
                    $ex->getMessage(), 500);
            return;
        }
        printf("%s - Terminado el procesamiento de mensajes especiales.\n", $this->now());
        return;
    }

}
