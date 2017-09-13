<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Directs extends MY_Controller {

    public function index()
    {
        $message = $this->input->post('message');
        
        $pks = $this->session->userdata('pks');
        
        $data['session'] = $this->session->userdata();
        $data['message'] = $message;
        
        $user_id = $this->session->pk;

        if ($user_id != NULL) {

            $this->load->library('directs/queue/manager');
            $dqm = new Manager();
            $added = $dqm->add($user_id, $this->getLocalDate(), $pks, $message);

            if ($added) {
                $last_direct = $dqm->get($user_id);
                $data['last_direct'] = $last_direct;
                $this->load->view('directs_dashboard', $data);
            }
            else {
                show_error('Something wrong happened trying to enqueue the message', 500);
            }
            
            return;
        }
        
        show_error('You are trying to enter a restricted area', 500);
    }

}
