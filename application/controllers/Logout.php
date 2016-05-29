<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends Yumbox_Controller {

	public function index()
	{
		$requestUrl = $this->input->get("redirect");
		
		$this->login_util->logout();
		redirect($requestUrl, 'refresh');
	}
}
