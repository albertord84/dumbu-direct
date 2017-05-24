<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function index()
	{
		$this->load->view('search_form');
	}

  /**
   * Typeahead espera JSON con comillas no con comillas simples...
   */
  public function users($query) {
    if ($query == 'johndoe') {
      echo "[ { \"name\": \"John Doe\" }, { \"name\": \"Jenny Doe\" } ]";
    }
    else {
      echo "[ { \"name\": \"John Doe 2\" }, { \"name\": \"Jenny Doe 2\" } ]";
    }
  }

  // Posibles metodos de la API a usar aqui:
  //
  // Instagram::getAutoCompleteUserList
  // Instagram::getSuggestedUsers
  // Instagram::getUserFollowers
  // Instagram::getUserFollowings
  // Instagram::searchFBUsers
  // Instagram::searchUsers

}
