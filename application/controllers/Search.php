<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public $instagram = NULL;

    public function index() {
        //if ($this->session->username !== NULL) {
            $this->load->view('search_followers_form', [
                'username' => $this->session->username,
                'is_admin' => $this->session->is_admin != NULL
            ]);
        /*} else {
            $this->load->view('login_form');
        }*/
    }

    public function followers($query) {
		if ($this->session->username !== NULL) {
			$user = $this->get_user_data($this->session->username);
			$instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
			//$instagram->setUser($this->session->username, $user->password);
			try {
				$instagram->login($this->session->username, $user->password);
			} catch (\Exception $ex) {
				$response = ['message' => $ex->getMessage()];
				return $this->output->set_content_type('application/json')
					->set_status_header(500)
					->set_output(json_encode($response));
			}
			try {
				$response = $instagram->people->search($query)->getUsers();
			} catch (Exception $e) {
				$response = ['message' => $e->getMessage()];
				return $this->output->set_content_type('application/json')
					->set_status_header(500)
					->set_output(json_encode($response));
			}
			return $this->output->set_content_type('application/json')
				->set_output(json_encode($response));
		} else {
			$this->load->view('login_form');
		}
	}

	public function get_user_data($username) {
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

	private function clean_previous_instagram_session($username)
	{
		$dir = INSTAGRAM_SESSIONS . '/' . $username;
		if (file_exists($dir)) {
			shell_exec("rm -r " . $dir);
		}
	}


}
