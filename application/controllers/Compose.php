<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Compose extends CI_Controller {

    public $instagram = NULL;

    public $permitted_ips = [ '127.0.0.1' ];

    public function index() {
        $this->getPermittedIps();
        if ($this->session->username !== NULL) {
            $this->session->follower_ids = $this->input->post('follower_ids');
            $this->session->follower_names = $this->input->post('follower_names');
            $this->load->view('compose_direct_message', []);
        } else {
            $this->load->view('login');
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

    public function getUserData($username) {
        $this->load->database();
        $this->db->where('username', $username);
        $query = $this->db->get('client');
        $result = $query->result();
        if (count($result) == 1) {
            return $result[0];
        } else {
            return NULL;
        }
    }

    public function insertStat($user_id, $follower_ids, $msg_id) {
        $this->load->database();
        foreach (explode(',', $follower_ids) as $follower_id) {
            $data = [
                'user_id' => $user_id,
                'follower_id' => $follower_id,
                'msg_id' => $msg_id
            ];
            $this->db->insert('stat', $data);
        }
    }

    public function messageAlreadyInserted($message)
    {
        $this->load->database();
        $this->db->where('msg_text', $message);
        $query = $this->db->get('message');
        $messages = $query->result();
        if (count($messages) == 0) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    public function insertMessage($user_id, $message) {
        $this->load->database();
        $data = [
            'user_id' => $user_id,
            'msg_text' => $message,
            'sent' => 0,
            'mass' => $this->input->post('massive') == NULL ? 0 : 1
        ];
        $this->db->insert('message', $data);
        return $data;
    }

    public function lockTask() {
        write_file(ROOT_DIR . '/var/task.lock', '');
    }

    public function unlockTask() {
        $lock_file = ROOT_DIR . '/var/task.lock';
        if (file_exists($lock_file)) {
            unlink($lock_file);
        }
    }

    public function lastMessageId() {
        $this->load->database();
        $this->db->select_max('id');
        $query = $this->db->get('message');
        $result = $query->result();
        if (count($result) == 1) {
            return $result[0]->id;
        } else {
            return NULL;
        }
    }

    public function createTask() {
        $this->getPermittedIps();
        set_time_limit(0);
        $username = $this->session->username;
        if ($username == NULL) {
            $this->load->view('login');
        }

        $message = trim($this->input->post('message'));
        if ($message !== NULL || $message !== '') {
            while (file_exists(ROOT_DIR . '/var/task.lock')) {
                sleep(5);
            }
            $this->lockTask();
            $user = $this->getUserData($username);
            if ($this->messageAlreadyInserted($message)) {
                $this->load->view('compose_direct_message', [
                    'error' => sprintf("This message: \"%s...\" "
                            . "was previously sent.<br><b>Avoid spam or service rejection.</b>",
                            substr($message, 0, 15))
                ]);
                $this->unlockTask();
                return;
            }
            $this->insertMessage($user->id, $message);
            $last_msg_id = $this->lastMessageId();
            if ($this->input->post('massive') == NULL) {
                $this->insertStat($user->id, $this->session->follower_ids, $last_msg_id);
            }
            $this->unlockTask();
            $this->load->view('message_dashboard', [
                'username' => $username,
                'message' => $message,
                'follower_names' => explode(',', $this->session->follower_names)
            ]);
            return;
        }
        else {
            $this->load->view('compose_direct_message', [
                'error' => 'El mensaje no puede estar vacío'
            ]);
            return;
        }
    }

}
