<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller {

    public function index()
    {
        echo 'OK';
    }
    
    public function dqmanager()
    {
        $uid = '3670825632';
        $pks = '4239955376,4492293740,4542814483';
        $msg = 'Mensaje 1';
        
        $this->load->library('directs/queue/manager');
        
        $dqm = new Manager();
        $added = $dqm->add($uid, $this->getLocalDate(), $pks, $msg);
        
        if ($added) {
            echo $dqm->get($uid);
        }
    }

}
