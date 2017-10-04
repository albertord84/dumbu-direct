<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public $instagram = NULL;

	public function index()
	{
		echo 'OK';
		//echo json_encode($this->getUserData('yordanoweb@yahoo.es'), JSON_PRETTY_PRINT);
	}

	public function getUserData($username)
	{
		$this->load->database();
    $this->db->where('username', $username);
    $query = $this->db->get('client');
		return $query->result();
	}

}
