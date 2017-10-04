<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Special extends CI_Controller {

    public $instagram = NULL;

    public $permitted_ips = [ '127.0.0.1' ];

    public function index() {
        set_time_limit(0);
        $this->getPermittedIps();
        $ip = $this->input->server('REMOTE_ADDR');
        if (in_array($ip, $this->permitted_ips)) {
            printf("\nProcesando mensajes...\n");
            try {
                $this->processMessages();
            } catch (Exception $ex) {
                show_error('Error al procesar los mensajes: ' .
                        $ex->getMessage(), 500);
                return;
            }
            printf("Terminado el procesamiento de mensajes.\n");
            return;
        }
        show_error('Access not permitted', 500);
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

    public function getMessageData($msg_id)
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

    public function sendMessage($msg_id)
    {
        $message = $this->getMessageData($msg_id);
        $user = $this->getUserData($message->user_id);
        $followers = $this->getMessageRecipients($message->user_id);
        if (count($followers)==0) {
            printf("El mensaje \"%s...\" ya se envio antes a los seguidores seleccionados\n",
                    substr($message->msg_text, 0, 15));
            return;
        }
        $this->loginInstagram($user);
        try {
            $this->instagram->directMessage($followers, $message->msg_text);
            printf("Enviado mensaje: \"%s...\"; a los seguidores [%s]\n",
                substr($message->msg_text, 0, 15), implode(',', $followers));
        }
        catch (Exception $ex) {
            $msg = sprintf("Error al enviar el mensaje \"%s...\"; ERROR: \"%s\"\n",
                substr($message->msg_text, 0, 15), $ex->getMessage());
            throw new Exception($msg, 500);
        }
    }

    public function getMessageRecipients($user_id)
    {
        $followers_file = ROOT_DIR . '/var/followers/' . $user_id;
        if (!file_exists($followers_file)) {
            throw new Exception('A followers list must be provided for sender with id '.
                $user_id . '. Contact system administrator.', 500);
        }
        $cmd = "head -n " . $followers_file;
        $data = trim(shell_exec($cmd));
        return explode(PHP_EOL, $data);
    }

    public function processMessages()
    {
        while(file_exists(ROOT_DIR . '/var/message.lock')) {
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
            $this->sendMessage($msg_id);
            $this->setMessageProcessing($msg_id, 0);
            $this->randomWait();
        }
        $this->unlockMessage();
    }

    public function getUserData($user_id) {
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
        $this->db->where('mass', 1);
        $this->db->limit(1);
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
            'sent_at' => \Carbon\Carbon::now()->timestamp
        ]);
    }

}
