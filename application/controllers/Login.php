<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

    public function index() {
        $user_id = $this->session->pk;
        $data['session'] = $this->session->userdata();

        if ($user_id != NULL) {
            $this->load->view('search_form', $data);
            return;
        }

        $this->load->view('login_form', $data);
    }

    private function verify_instagram($user, $pass) {
        $debug = false;
        $truncatedDebug = false;
        
        $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

        $ig->setUser($user, $pass);

        $response = $ig->login();
        $this->session->pk = $ig->account_id;

        // Cerrar la sesion en Instagram porque la API
        // la deja siempre abierta
        $ig->logout();
        
        $r = [
            'response' => $response,
            'account_id' => $this->session->pk
        ];

        return $r;
    }

    public function auth() {
        $username = $this->postVar('username');
        $password = $this->postVar('password');

        if ($username == NULL || $password == NULL) {
            show_error('You have not provided the credentials (Username and Password)', 500);
            return;
        }

        try {
            $resp = $this->verify_instagram($username, $password);
            
            $this->session->username = $username;
            $this->session->password = $password;

            $r = [
                'status' => 'OK',
                'response' => $resp,
                'username' => $username,
                'pk' => $this->session->pk
            ];
            echo json_encode($r);

            return;
        } catch (\Exception $ex) {
            $r = [
                'status' => 'SOME_ERR', 
                'response' => $resp,
                'username' => $username
            ];
            echo json_encode($r);
            return;
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        $data['session'] = $this->session->userdata();
        $data['session']['password'] = d_guid();
        $this->load->view('login_form', $data);
    }
    
    public function checkid() {
        $uid = $this->postVar('user_id');
        
        $response = NULL;
        
        $user_id = $this->session->pk;
        
        if (strcmp($user_id, $uid) == 0) {
            $response = array('success' => TRUE);
        }
        else {
            $response = array('success' => FALSE);
        }

        echo json_encode($response);
    }

}
