<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Compose extends CI_Controller {

    public $instagram = NULL;

    public $permitted_ips = [ '127.0.0.1' ];

    public function index() {
        /*if (true) {
            return $this->load->view('compose_message', [
                'username' => 'yordanoweb',
                'profiles' => '12341234,768567856,324564562',
                'usernames' => 'adidas,microsoft,cocacola'
            ]);
        }*/
        if ($this->session->username !== NULL) {
            $this->session->profiles = $this->input->post('profiles');
            $this->session->usernames = $this->input->post('usernames');
            if ($this->session->usernames === NULL ||
				$this->session->profiles === NULL) {
				return $this->load->view('errors/html/error_general', [
					'heading' => 'Missing data',
					'message' => 'You have to select followers first. Go to '.
						sprintf("<a href=\"%s\">", site_url('search')).
						'search</a>...'
				]);
			}
            return $this->load->view('compose_message', [
                'username' => $this->session->username,
                'profiles' => $this->session->profiles,
                'usernames' => $this->session->usernames
            ]);
        } else {
            $this->load->view('login_form');
        }
    }

    private function getUserData($username) {
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

    private function insertMessage($user_id, $message) {
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

    private function lockTask() {
        write_file(ROOT_DIR . '/var/task.lock', '');
    }

    private function unlockTask() {
        $lock_file = ROOT_DIR . '/var/task.lock';
        if (file_exists($lock_file)) {
            unlink($lock_file);
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

    public function send() {
        set_time_limit(0);
        $username = $this->session->username;
        if ($username == NULL) {
            return $this->access_not_allowed();
        }
        $message = trim($this->input->post('message'));
        if ($message === '' || $message === NULL) {
            return $this->load->view('errors/html/error_general', [
				'heading' => 'Missing data',
				'message' => 'You have to define the text to be sent. '.
					sprintf("Go <a href='%s'>back</a> and write something...",
						site_url('compose/message'))
			]);
        }
        while (file_exists(ROOT_DIR . '/var/task.lock')) {
            sleep(5);
        }
        $this->lockTask();
        $user = $this->getUserData($username);
        $this->insertMessage($user->id, $message);
		$this->insertStat($user->id, $this->session->follower_ids,
			$this->lastMessageId());
        $this->unlockTask();
		return $this->load->view('message_dashboard', [
			'username' => $username,
			'message' => $message,
			'is_admin' => $this->session->is_admin == NULL ? FALSE : TRUE,
			'follower_names' => explode(',', $this->session->follower_names)
		]);
    }

	private function insertStat($user_id, $follower_ids, $msg_id) {
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

	private function lastMessageId() {
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

}
