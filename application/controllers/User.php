<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    
    public $instagram = NULL;
    
    public $permitted_ips = [ '127.0.0.1' ];
    
    public function index() {
        return $this->load->view('login_form', []);
    }
    
    public function login() {
        return $this->load->view('login_form', []);
    }
    
    public function auth() {
        set_time_limit(0);
        
        if ($this->session->username !== NULL) {
            $this->load->view('search_followers');
        }
        
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        
        // Aqui se debe limpiar la sesion anterior
        sleep(5);
        
        $instagram = new \InstagramAPI\Instagram(FALSE, TRUE);
        $instagram->setUser($username, $password);
        
        try {
            date_default_timezone_set(TIME_ZONE);
            set_time_limit(0);
            $instagram->login();
            $this->session->pk = $instagram->account_id;
            $this->session->username = $username;
            // Determinar priv del usuario si esta en la BD
            $response = [
                'success' => TRUE,
                'pk' => $instagram->account_id,
                'username' => $username,
                //'priv' => $this->is_admin($username) ? 1 : 0
            ];
        } catch (Exception $e) {
            $response = [ 'success' => FALSE, 'message' => $e->getMessage() ];
        }
        
        $this->output->set_content_type('application/json')
            ->set_status_header($response['success'] ? 200 : 500)
            ->set_output(json_encode($response));
    }
    
}
