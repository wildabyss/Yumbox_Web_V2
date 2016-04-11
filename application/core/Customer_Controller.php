<?php

class Customer_Controller extends CI_Controller {
	protected function header(){
		// load language
		$this->lang->load("header");
		
		// view content
		$data["slogan"] = $this->lang->line("slogan");
		$data["sign_in_button"] = $this->lang->line("sign_in_button");
		$data["vendor_button"] = $this->lang->line("vendor_button");
		
		// Load views
		$this->load->view("/templates/common_header");
		$this->load->view("/templates/customer_top", $data);
	}
	
	protected function footer(){
		// load language
		$this->lang->load("footer");
		
		// Load views
		$this->load->view("/templates/common_footer");
	}
}