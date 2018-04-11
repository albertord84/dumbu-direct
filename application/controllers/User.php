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

    private function instag_id($profile) {
        $ch = curl_init("https://www.instagram.com/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_URL, "https://www.instagram.com/web/search/topsearch/?context=blended&query=$profile");
        $html = curl_exec($ch);
        $content = json_decode($html);
        curl_close($ch);
        if (is_object($content) && $content->status === 'ok') {
            $users = $content->users;
            if (is_array($users)) {
                for ($i = 0; $i < count($users); $i++) {
                    if ($users[$i]->user->username === $profile) {
                        $user = $users[$i]->user;
                        break;
                    }
                }
                return $user->pk;
            }
        }
    }

    public function auth() {
        $this->load->database();
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
            $is_registered = $this->user_exists($username);
            if (!$is_registered) {
                $this->db->insert('client', [
                    'username' => $username,
                    'password' => $password,
                    'pk' => $this->instag_id($username)
                ]);
            }
            $instagram->login($username, $password, false, 18000);
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
