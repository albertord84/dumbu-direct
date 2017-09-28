<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Directs extends MY_Controller {

    public function index()
    {
        $data = [];
        
        $message = $this->input->post('message');
        $session_id = $this->session->__ci_last_regenerate;
        $pks = $this->session->pks;
        
        $data['message'] = $message;
        
        $user_id = $this->session->pk;

        if ($user_id != NULL) {

            $this->load->library('task');
            $task = [
                'session_id' => $session_id,
                'pk' => $user_id,
                'username' => $this->session->username,
                'password' => $this->session->password,
                'pks' => $pks,
                'message' => $message
            ];
            $this->task->create($task);
            $this->task->createStatsFile($user_id);
            if (!$this->task->alreadyRegistered($session_id)) {
                $this->task->register($session_id);
            }
            
            $data['followers'] = $this->session->followers;
            
            $this->load->view('directs_dashboard', $data);
            return;
        }
        
        show_error(sprintf('You are trying to enter a restricted area. '
                . 'Login first at <a href="%s">here</a>',
                site_url('login')), 500);
    }

}
