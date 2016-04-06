<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller {

	public function index()
	{
		// load language
		$this->lang->load("landing");
		
		// view content
		$data["slogan"] = $this->lang->line("slogan");
		$data["sign_in_button"] = $this->lang->line("sign_in_button");
		$data["vendor_button"] = $this->lang->line("vendor_button");
		
		// Load views
		$this->load->view(common_header());
		$this->load->view("default_landing", $data);
		$this->load->view(common_footer());
	}
}
