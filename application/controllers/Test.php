<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller {

    public function index()
    {
        echo 'OK';
    }
    
    public function last()
    {
        $this->load->library('task');
        echo "OK " . $this->task::findLast();
    }

}
