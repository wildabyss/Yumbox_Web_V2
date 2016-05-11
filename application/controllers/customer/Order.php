<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Yumbox_Controller {
	public function add($food_id=false){
		// get food data
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false){
			show_404();
		}
		
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$requestUrl = $this->input->get("redirect", true);
			redirect("/login?redirect=".urlencode($requestUrl), 'refresh');
		}
		
		
	}
	
	public function index()
	{
		$this->explore();
	}
}
