<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends Yumbox_Controller {

	public function index($requestUrl="menu")
	{
		$this->session->sess_destroy();
		redirect($requestUrl, 'refresh');
	}
}
