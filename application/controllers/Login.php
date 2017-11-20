<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public $instagram = NULL;

    public $postdata = NULL;

    public function index() {
        if ($this->session->username) {
            $this->load->view('search_followers', [
                'is_admin' => $this->is_admin($this->session->username),
                'username' => $this->session->username
            ]);
            return;
        }
        $this->load->view('login');
    }

    public function logged() {
        if ($this->session->username !== NULL) {
            return $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'success' => TRUE,
                    'username' => $this->session->username,
                    'is_admin' => $this->is_admin($this->session->username)
                ]));
        }
        else {
            return $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => FALSE
                ]));
        }
    }

    public function admin() {
        if ($this->session->username !== NULL) {
            return $this->output->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'is_admin' => $this->is_admin($this->session->username)
                ]));
        }
        else {
            return $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => FALSE
                ]));
        }
    }

    public function userExists($username) {
        $this->load->database();
        $this->db->where('username', $username);
        $query = $this->db->get('client');
        return $query->num_rows() == 0 ? FALSE : TRUE;
    }

    public function insertToDb($username, $password, $pk) {
        $this->load->database();
        $data = [
            'username' => $username,
            'password' => $password,
            'pk' => $pk,
            'created_at' => \Carbon\Carbon::now()->timestamp
        ];
        $this->db->insert('client', $data);
    }

    public function getXhrParam($param)
    {
        // Tiene que ser asi, porque el POST generado por AngularJS
        // difiere del POST normal de un formulario
        if ($this->postdata == NULL){
            $this->postdata = file_get_contents("php://input");
        }
        $request = json_decode($this->postdata, TRUE);
        return $request[$param];
    }

    public function auth() {
        set_time_limit(0);
        
        if ($this->session->username !== NULL) {
            $this->load->view('search_followers');
        }

        $username = $this->getXhrParam('username');
        $password = $this->getXhrParam('password');

        $this->cleanInstagramApiSession($username);
        sleep(5);

        $instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
        $instagram->setUser($username, $password);

        try {
            date_default_timezone_set(TIME_ZONE);
            set_time_limit(0);
            $instagram->login();
            $this->session->pk = $instagram->account_id;
            $this->session->username = $username;
            if (!$this->userExists($username)) {
                $this->insertToDb($username, $password, $instagram->account_id);
            }
            else {
                $this->session->is_admin = $this->is_admin($username);
            }
            $response = [
                'success' => TRUE,
                'pk' => $instagram->account_id,
                'username' => $username,
		'priv' => $this->is_admin($username) ? 1 : 0
            ];
        } catch (Exception $e) {
            $response = [ 'success' => FALSE, 'message' => $e->getMessage() ];
        }

        $this->output->set_content_type('application/json')
                ->set_status_header($response['success'] ? 200 : 500)
                ->set_output(json_encode($response));
    }
    
    public function is_admin($username)
    {
        $this->load->database();
        $this->db->where('username', $username);
        $query = $this->db->get('client');
        $users = $query->result();
        $is_admin = $users[0]->priv == 1;
        return $is_admin;
    }

    public function cleanInstagramApiSession($username)
    {
        $dir = INSTAGRAM_SESSIONS . '/' . $username;
        if (file_exists($dir)) {
            shell_exec("rm -r " . $dir);
        }
    }

    public function logout() {
        if ($this->session->username !== NULL) {
            $this->cleanInstagramApiSession($this->session->username);
        }
        session_destroy();
        $this->session->username = NULL;
        $this->session->set_userdata([]);
        $this->load->view('login');
    }

}
