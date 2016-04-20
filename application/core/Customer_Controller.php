<?php

class Customer_Controller extends CI_Controller {
	protected function header(){
		// load language
		$this->lang->load("header");
		
		// view content
		$data["slogan"] = $this->lang->line("slogan");
		$data["vendor_button"] = $this->lang->line("vendor_button");
		
		// determine if we have a valid session
		if (isset($_SESSION['fb_token']) || isset($_SESSION['google_token'])){
			// we have a valid session
			$data["sign_out_button"] = $this->lang->line("sign_out_button");
			$data["sign_out_link"] = "/logout";
		} else {
			$data["sign_in_button"] = $this->lang->line("sign_in_button");
			$data["sign_in_link"] = "/login";
		}
				
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