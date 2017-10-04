<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler extends CI_Controller {

    public $instagram = NULL;

    public $permitted_ips = [ '127.0.0.1' ];

    public function index() {
        set_time_limit(0);
        $this->denyIfNotPermitted();
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
        return trim(shell_exec("date \"+%d/%b %r\""));
    }
    
    public function denyIfNotPermitted()
    {
        $this->getPermittedIps();
        $ip = $this->input->server('REMOTE_ADDR');
        if (!in_array($ip, $this->permitted_ips)) {
            show_error('Access not permitted', 500);
        }
    }

    public function getPermittedIps()
    {
        if (file_exists(ROOT_DIR . '/etc/permitted_ips')) {
            $data = read_file(ROOT_DIR . '/etc/permitted_ips');
            $lines = explode(PHP_EOL, $data);
            foreach ($lines as $ip) {
                if (trim($ip) == '' || $ip == '127.0.0.1') {
                    continue;
                }
                $this->permitted_ips[] = $ip;
            }
        }
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
        printf("Esperando %s segs para el siguiente envio\n", $secs);
        sleep($secs);
    }
    
    public function setMessageFailed($msg_id)
    {
        $this->load->database();
        $this->db->where('id', $msg_id);
        $this->db->update('message', [ 'failed' => 1 ]);
    }

    public function sendMessage($msg_id)
    {
        $message = $this->getMessage($msg_id);
        $user = $this->getUser($message->user_id);
        $followers = $this->getMessageRecipients($msg_id);
        if (count($followers)==0) {
            printf("El mensaje \"%s...\" ya se envio antes a los seguidores seleccionados\n",
                    substr(trim($message->msg_text), 0, 15));
            return;
        }
        $this->loginInstagram($user);
        try {
            $this->instagram->directMessage($followers, $message->msg_text);
            printf("Enviado mensaje: \"%s...\"; a los seguidores [%s]\n",
                substr(trim($message->msg_text), 0, 15), implode(',', $followers));
        }
        catch (Exception $ex) {
            $msg = sprintf("Error al enviar el mensaje \"%s...\"; ERROR: \"%s\"\n",
                substr(trim($message->msg_text), 0, 15), $ex->getMessage());
            $this->setMessageFailed($msg_id);
            //$this->cleanInstagramApiSession($user->username);
            throw new Exception($msg, 500);
        }
        //$this->cleanInstagramApiSession($user->username);
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
        while(file_exists(ROOT_DIR . '/var/message.lock')) {
            printf("Se quedo bloqueado el acceso a la cola de mensajes\n");
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
        while(file_exists(ROOT_DIR . '/var/message.lock')) {
            printf("Se quedo bloqueado el acceso a la cola de mensajes\n");
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
                    substr(trim($message->msg_text), 0, 15));
                continue;
            }
            $followers = $this->getSpecialMessageRecipients($message->id);
            if ($followers == NULL) {
                printf("Ya se envio este mensaje \"%s...\" a toda la lista de seguidores\n",
                    substr(trim($message->msg_text), 0, 15));
                $this->setMessageSent($message->id);
                continue;
            }
            try {
                $this->getInstagram();
                $this->setMessageProcessing($message->id, 1);
                $this->randomWait();
                $this->sendSpecialMessage($message->id, $followers);
                $this->setMessageProcessing($message->id, 0);
                $this->setMessageSent($message->id);
                $this->popAlreadyTexted($pk, $followers);
                $this->insertSpecialMessageStat($message->id, $followers);
            } catch (Exception $ex) {
                printf("No se pudo enviar el mensaje especial \"%s...\"\n",
                    substr(trim($message->msg_text), 0, 15));
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
        $this->db->where('mass', 0);
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
    }
    
    public function lastSpecialMessages() {
        $this->load->database();
        $this->db->where('processing', 0);
        $this->db->where('sent', 0);
        $this->db->where('mass', 1);
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
    
    public function randomGreeting()
    {
        $greetings = [
            0 => "Oi",
            1 => "Olá",
            2 => "Bom dia",
            3 => "Como vai tudo?"
        ];
        $n = mt_rand(0, count($greetings) - 1);
        return $greetings[$n];
    }
    
    public function sendSpecialMessage($msg_id, $followers)
    {
        $message = $this->getMessage($msg_id);
        $user = $this->getUser($message->user_id);
        $this->loginInstagram($user);
        try {
            $this->instagram->directMessage($followers, $this->randomGreeting());
            $this->instagram->directMessage($followers, $message->msg_text);
            printf("Enviado mensaje: \"%s...\"; a los seguidores [%s]\n",
                substr(trim($message->msg_text), 0, 15), implode(',', $followers));
        } catch (Exception $ex) {
            $msg = sprintf("Error al enviar el mensaje \"%s...\"; ERROR: \"%s\"\n",
                substr(trim($message->msg_text), 0, 15), $ex->getMessage());
            //$this->cleanInstagramApiSession($user->username);
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
                'msg_id' => $message->id
            ];
            $this->db->insert('stat', $data);
        }
    }

    public function special()
    {
        set_time_limit(0);
        $this->denyIfNotPermitted();
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
