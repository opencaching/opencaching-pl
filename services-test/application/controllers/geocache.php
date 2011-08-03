<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Geocache extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

	}

	function index()
	{
		$this->load->helper('url');
		$this->load->view('geocache');
		
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */