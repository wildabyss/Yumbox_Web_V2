<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends Customer_Controller {

	public function index()
	{
		// load language
		$this->lang->load("landing");
		
		// page data
		$data["quick_menu_text"] = $this->lang->line("quick_menu_text");
		$data["full_menu_text"] = $this->lang->line("full_menu_text");
		
		// Load views
		$this->header();
		$this->load->view("landing", $data);
		$this->footer();
	}
}
