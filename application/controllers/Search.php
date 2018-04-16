<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public $instagram = NULL;

    public function index() {
        if ($this->session->username !== NULL) {
            $this->load->view('search_followers', [
                'username' => $this->session->username,
                'is_admin' => $this->session->is_admin != NULL
            ]);
        } else {
            redirect('/user/login', 'location');
        }
    }

    private function search_profiles($query) {
        $ch = curl_init("https://www.instagram.com/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_URL, "https://www.instagram.com/web/search/topsearch/?context=blended&query=$query");
        $html = curl_exec($ch);
        $content = json_decode($html);
        curl_close($ch);
        if (is_object($content) && $content->status === 'ok') {
            $users = $content->users;
            if (is_array($users)) {
            	$result = array_map(function($user) {
            		return $user->user;
            	}, $users);
                return $this->output->set_content_type('application/json')
					->set_status_header(200)
					->set_output(json_encode($result));
            }
        }
        else {
            return $this->output->set_content_type('application/json')
				->set_status_header(500)
				->set_output(json_encode([
					'success' => false,
					'message' => 'Something happened with your query. ' .
						'Contact system administration.'
				]));
        }
    }

    public function followers($query) {
    	// Buscar sin autenticar en Instagram
    	if (true) return $this->search_profiles($query);

		if ($this->session->username !== NULL) {
			$user = $this->get_user_data($this->session->username);
			$instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
			try {
				$instagram->login($this->session->username, $user->password, false, 10800);
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
