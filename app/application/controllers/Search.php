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
    if (preg_match("/$query/", 'johndoe') == 1) {
      echo "[ { \"name\": \"John Doe\" }, { \"name\": \"Jenny Doe\" } ]";
      return;
    }
    if (preg_match("/$query/", 'abcdefg') == 1) {
      echo "[ " . 
        "{ \"name\": \"Arlen\" }, { \"name\": \"Bell\" }, " . 
        "{ \"name\": \"Carl\" }, { \"name\": \"Dan\" }, " . 
        "{ \"name\": \"Erskine\" }, { \"name\": \"Fabian\" }, " . 
        "{ \"name\": \"Gary\" }, { \"name\": \"Gus\" } " . 
      " ]";
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
