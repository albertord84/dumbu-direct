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
                'ci_last_regenerate' => $this->session->__ci_last_regenerate,
                'pk' => $user_id,
                'username' => $this->session->username,
                'dest' => $pks,
                'message' => $message
            ];
            $this->task->create($task);
            $this->task->saveFollowersList($user_id, $pks);
            $this->task->createStatsFile($user_id);
            
            $data['followers'] = $this->session->followers;
            
            $data['task'] = $task;
            $this->session->task = $task;
            
            $this->load->view('directs_dashboard', $data);
            return;
        }
        
        show_error(sprintf('You are trying to enter a restricted area. '
                . 'Login first at <a href="%s">here</a>',
                site_url('login')), 500);
    }

}
