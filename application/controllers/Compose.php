<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Compose extends CI_Controller {

    public $instagram = NULL;

    public $permitted_ips = [ '127.0.0.1' ];

    public function index() {
        if ($this->session->username !== NULL) {
            $this->session->follower_ids = $this->input->post('follower_ids');
            $this->session->follower_names = $this->input->post('follower_names');
            if ($this->session->follower_names === NULL ||
				$this->session->follower_ids === NULL) {
				return $this->load->view('errors/html/error_general', [
					'heading' => 'Missing data',
					'message' => 'You have to select followers first. Go to '.
						sprintf("<a href=\"%s\">", site_url('search')).
						'search</a>...'
				]);
			}
            $this->load->view('compose_direct_message', [
                'username' => $this->session->username,
                'is_admin' => $this->session->is_admin != NULL,
                'follower_ids' => $this->session->follower_ids,
                'follower_names' => $this->session->follower_names
            ]);
        } else {
            $this->load->view('login');
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
            'promo' => 0,
            'processing' => 0,
            'failed' => 0,
            'sent_at' => 0
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

    private function access_not_allowed() {
        $this->output->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode([
                'success' => FALSE,
                'message' => 'Access not allowed for your privileges level'
            ], JSON_PRETTY_PRINT));
    }

    public function getXhrParam($param) {
        // Tiene que ser asi, porque el POST generado por AngularJS
        // difiere del POST normal de un formulario
        if ($this->postdata == NULL){
            $this->postdata = file_get_contents("php://input");
        }
        $request = json_decode($this->postdata, TRUE);
        return $request[$param];
    }

    public function send() {
        set_time_limit(0);
        $username = $this->session->username;
        if ($username == NULL) {
            return $this->access_not_allowed();
        }
        $message = trim($this->input->post('message'));
        if ($message === '' || $message === NULL) {
            $message = $this->getXhrParam('message');
        }
        while (file_exists(ROOT_DIR . '/var/task.lock')) {
            sleep(5);
        }
        $this->lockTask();
        $user = $this->getUserData($username);
        $this->insertMessage($user->id, $message);
        $last_msg_id = $this->lastMessageId();
        $this->unlockTask();
        return $this->output->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => TRUE,
                'message' => 'Message sent to selected followers'
            ], JSON_PRETTY_PRINT));
    }

    public function createTask() {
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
                'is_admin' => $this->session->is_admin == NULL ? FALSE : TRUE,
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
