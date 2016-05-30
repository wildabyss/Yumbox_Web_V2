<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Yumbox_Controller {
	
	/**
	 * GET method that displays the logged in user's profile page by default
	 */
	public function index()
	{
		$this->load->view("/vendor/dashboard");
	}
}
