<?php

class Yumbox_Controller extends CI_Controller {

	/**
	 * Display the top navigation bar
	 *
	 * @argument $display If true, display the content; if false, return as string
	 */
	protected function navigation($display=true){
		// load language
		$this->lang->load("header");
		
		// view content
		$data["vendor_button"] = $this->lang->line("vendor_button");
		
		// determine if we have a valid session
		if ($this->login_util->isUserLoggedIn()){
				
			$user_id = $this->login_util->getUserId();
			$user = $this->user_model->getUserForUserId($user_id);
			if ($user != NULL){
				// we have a valid session
				$data["user_name"] = $user->name;
				$data["sign_out_link"] = "/logout";
				
				// see if user is a chef
				if ($this->user_model->isUserAChef($user_id))
					$is_vendor = true;
				else
					$is_vendor = false;
				
				// retrieve total orders in the open basket
				$open_basket = $this->order_basket_model->getOrCreateOpenBasket($user_id);
				$order_count = $this->order_basket_model->getTotalOrdersInBasket($open_basket->id);
				if ($order_count===false) $order_count = 0;
				
				// bind data
				$data["order_count"] = $order_count;
				$data["is_vendor"] = $is_vendor;
				
				return $this->load->view("navigation", $data, !$display);
			}
		}
		
		// get current URL
		$current_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		// show log in button
		$data["sign_in_button"] = $this->lang->line("sign_in_button");
		$data["sign_in_link"] = "/login?redirect=".urlencode($current_url);
		
		return $this->load->view("navigation", $data, !$display);
	}
	
	/**
	 * Display the header common to Yumbox web application
	 */
	protected function header($display=true){
		// get user location
		$user_id = $this->login_util->getUserId();
		$location = $this->search->getUserCoordinates($user_id);
		
		// bind data
		$data['location'] = $location;
				
		// Load views
		$this->load->view("header", $data, !$display);
	}
	
	/**
	 * Display the footer common to Yumbox web application
	 */
	protected function footer($display=true){	
		// Load views
		$this->load->view("footer", array(), !$display);
	}
}
