<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public $instagram = NULL;
    
    public $postdata = NULL;

    public function index() {
        if ($this->session->username) {
            $this->load->view('search_followers');
            return;
        }
        $this->load->view('login');
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
        if ($this->session->username !== NULL) {
            $this->load->view('search_followers');
        }

        $username = $this->getXhrParam('username');
        $password = $this->getXhrParam('password');
        
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
            $response = [
                'success' => TRUE,
                'pk' => $instagram->account_id,
                'username' => $username
            ];
        } catch (Exception $e) {
            $this->cleanInstagramApiSession($username);
            $response = [ 'success' => FALSE, 'message' => $e->getMessage() ];
        }

        $this->output->set_content_type('application/json')
                ->set_status_header($response['success'] ? 200 : 500)
                ->set_output(json_encode($response));
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
        $this->session->set_userdata([]);
        $this->load->view('login');
    }

}
