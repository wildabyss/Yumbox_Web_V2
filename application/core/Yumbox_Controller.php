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
				
				return $this->load->view("common_nav", $data, !$display);
			}
		}
		
		// show log in button
		$data["sign_in_button"] = $this->lang->line("sign_in_button");
		$data["sign_in_link"] = "/login";
		
		return $this->load->view("common_nav", $data, !$display);
	}
	
	/**
	 * Display the header common to Yumbox web application
	 */
	protected function header(){
		// get user location
		$user_id = $this->login_util->getUserId();
		$location = $this->search->getUserCoordinates($user_id);
		
		// bind data
		$data['location'] = $location;
				
		// Load views
		$this->load->view("common_header", $data);
	}
	
	/**
	 * Display the footer common to Yumbox web application
	 */
	protected function footer(){	
		// Load views
		$this->load->view("common_footer");
	}
}
