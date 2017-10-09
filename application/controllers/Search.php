<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public $instagram = NULL;

    public function index() {
        if ($this->session->username !== NULL) {
            $this->load->view('search_followers', [
                'is_admin' => $this->session->is_admin != NULL
            ]);
        } else {
            $this->load->view('login');
        }
    }
    
    public function cleanInstagramApiSession($username)
    {
        $dir = INSTAGRAM_SESSIONS . '/' . $username;
        if (file_exists($dir)) {
            shell_exec("rm -r " . $dir);
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

    /**
     * Typeahead espera JSON con comillas no con comillas simples...
     */
    public function users($query) {
        $username = $this->session->username;

        $response = [
                ["username" => "John Doe"],
                ["username" => "Johnny Doe"],
                ["username" => "Johnson Doe"],
                ["username" => "Johnna Doe"],
                ["username" => "Johns Doe"],
                ["username" => "Jonky Doe"],
        ];

        if ($query == 'johndoe') {
            $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
            return;
        }

        try {
            $user = $this->getUserData($username);
            if ($user == NULL) {
                throw new Exception("Non existent user " . $username, 1);
            }
        } catch (Exception $e) {
            $response = ['message' => $ex->getMessage()];
            $this->output->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode($response));
            return;
        }

        $instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
        $instagram->setUser($user->username, $user->password);

        try {
            $instagram->login();
        } catch (\Exception $ex) {
            $response = ['message' => $ex->getMessage()];
            $this->output->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode($response));
            return;
        }

        try {
            $response = $instagram->searchUsers($query)->users;
        } catch (Exception $e) {
            $response = ['message' => $ex->getMessage()];
            $this->output->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode($response));
            return;
        }

        $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
    }

}
