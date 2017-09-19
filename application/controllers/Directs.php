<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Directs extends MY_Controller {

    public function index()
    {
        $message = $this->input->post('message');
        
        $pks = $this->session->pks;
        
        $data['session'] = $this->session->userdata();
        $data['message'] = $message;
        
        $user_id = $this->session->pk;

        if ($user_id != NULL) {

            $this->load->library('task');
            $task = [
                'pk' => $user_id,
                'username' => $this->session->username,
                'dest' => $pks,
                'message' => $message
            ];
            $this->task->create($task);
            
            return;
        }
        
        show_error('You are trying to enter a restricted area', 500);
    }

}
