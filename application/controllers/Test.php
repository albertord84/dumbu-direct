<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller {

    public function index()
    {
		$this->load->database();
        $this->db->where('pk', '3a670825632');
        $query = $this->db->get('client');
        var_dump($query->num_rows());
	}

}
