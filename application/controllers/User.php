<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    
    public $instagram = NULL;
    
    public $permitted_ips = [ '127.0.0.1' ];
    
    public function index() {
    	if ($this->session->username !== NULL) {
			return $this->load->view('search_followers_form', []);
		}
        return $this->load->view('login_form', []);
    }
    
    public function login() {
        return $this->index();
    }
    
    public function auth() {
        set_time_limit(0);
        
        if ($this->session->username !== NULL) {
            $this->load->view('search_followers');
        }
        
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        
        $this->clean_previous_instagram_session($username);
        sleep(5);
        
        $instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
        
        try {
            set_time_limit(0);
            $instagram->login($username, $password, true);
            $this->session->pk = $instagram->account_id;
            $this->session->username = $username;
            $is_admin = $this->is_admin($username);
			$this->session->is_admin = $is_admin;
            $response = [
                'success' => TRUE,
                'pk' => $instagram->account_id,
                'username' => $username,
                'priv' => $is_admin ? 1 : 0
            ];
        } catch (Exception $e) {
            $response = [ 'success' => FALSE, 'message' => $e->getMessage() ];
        }
        
        $this->output->set_content_type('application/json')
            ->set_status_header($response['success'] ? 200 : 500)
            ->set_output(json_encode($response));
    }

	private function is_admin($username)
	{
		$this->load->database();
		$this->db->where('username', $username);
		$query = $this->db->get('client');
		$users = $query->result();
		$is_admin = $users[0]->priv == 1;
		return $is_admin;
	}

	private function user_exists($username) {
		$this->load->database();
		$this->db->where('username', $username);
		$query = $this->db->get('client');
		return $query->num_rows() == 0 ? FALSE : TRUE;
	}

	private function clean_previous_instagram_session($username)
	{
		$dir = INSTAGRAM_SESSIONS . '/' . $username;
		if (file_exists($dir)) {
			shell_exec("rm -r " . $dir);
		}
	}

	public function logout() {
		if ($this->session->username !== NULL) {
			$this->clean_previous_instagram_session($this->session->username);
		}
		session_destroy();
		$this->session->set_userdata([]);
		$this->load->view('login_form');
	}
}
