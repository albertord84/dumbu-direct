<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller {

    public function index()
    {
        echo 'OK';
    }
    
    public function add_direct()
    {
        $uid = '3670825632';
        $pks = '4239955376,4492293740,4542814483';
        $msg = 'Mensaje 11';
        
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        $added = $dqm->add($uid, $this->getLocalDate(), $pks, $msg);
        
        if ($added) {
            echo $dqm->get($uid);
        }
    }

    public function check_pk($pk)
    {
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        $added = $dqm->pk_taken($pk);
        
        if ($added) {
            echo 'YES'; 
        }
        else {
            echo 'NO';
        }
    }
    
    public function has_user($uid)
    {
        //echo 'OK'; if (TRUE) exit(0);
        
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        $exists = $dqm->exists($uid);
        
        if ($exists) {
            echo 'YES'; 
        }
        else {
            echo 'NO';
        }
    }

    public function msg_count()
    {
        //echo 'OK'; if (TRUE) exit(0);
        
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        echo $dqm->queue_count();
    }

    public function msg_page($p, $c)
    {
        //echo 'OK'; if (TRUE) exit(0);
        
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        echo json_encode( $dqm->msg_page($p, $c) );
    }
    
    public function get_obj($id)
    {
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        print_r( json_decode( $dqm->get($id) ) );
    }
    
    public function last_sent()
    {
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        
        $last = $dqm->last_sent();
        if ($last) {
            print_r($last);
        }
        else echo 'NO';
    }
    
    public function set_last()
    {
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        
        $dqm->set_last('20170711_133706_3670825632.json', 3);
    }

}
