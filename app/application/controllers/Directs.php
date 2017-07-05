<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Directs extends MY_Controller {

    public function index()
    {
        $message = $this->input->post('message');
        
        $data['session'] = $this->session->userdata();
        $data['message'] = $message;
        
        $user_id = $this->session->userdata('user_id');

        if ($user_id != NULL) {
            $this->load->view('directs_dashboard', $data);
            return;
        }
        
        show_error('You are trying to enter a restricted area', 500);
    }

}
