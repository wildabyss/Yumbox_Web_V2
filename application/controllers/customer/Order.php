<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Yumbox_Controller {
	/**
	 * Attempt to fetch the open basket for $user_id
	 * Create one if it doesn't exist
	 */
	protected function getOrCreateOpenBasket($user_id){
		do {
			$order_basket = $this->order_basket_model->getOpenBasketForUser($user_id);
			if ($order_basket === false){
				// create one if it doesn't exist
				$res = $this->order_basket_model->createOpenBasketForUser($user_id);
				if ($res !== true){
					throw new Exception($error);
					return;
				}
			}
		} while ($order_basket === false);
		
		return $order_basket;
	}
	
	/**
	 * Add order to the un-paid (open) order_basket
	 * When done, redirect back to redirect GET parameter
	 */
	public function add($food_id=false){
		$requestUrl = $this->input->post("redirect", true);
		
		// get food data
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false){
			show_404();
		}
		
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode($requestUrl), 'refresh');
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		// fetch open order basket
		$open_basket = $this->getOrCreateOpenBasket($user_id);
		
		// add order to this basket
		$res = $this->order_model->addOrderToBasket($food_id, $order_basket->id);
		if ($res !== true){
			throw new Exception($error);
			return;
		}
		
		// at this point, add order has succeeded, return to where we came from
		redirect($requestUrl, 'refresh');
	}

	public function basket($basket_id=0){
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode('/customer/order'), 'refresh');
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		if ($basket_id == 0){
			// show the order history
			
			
			
			// Load views
			$this->header();
			$this->navigation();
			$this->load->view("customer/checkout");
			$this->footer();
		} else {
			// get a particular order basket
			
			// fetch the basket with $basket_id
			$order_basket = $this->order_basket_model->getOrderBasketForUser($basket_id, $user_id);
			
			if ($order_basket === false){
				show_404();
			} else {
				// is this the open basket?
				$is_open_basket = $order_basket->payment_id =="";
				
				// get vendor information
				$vendors = $this->order_basket_model->getAllVendorsInBasket($basket_id);
				
				
				
				
				
				// bind data
				$data["is_open_basket"] = $is_open_basket;
				$data["vendors"] = $vendors;
				
				// Load views
				$this->header();
				$this->navigation();
				$this->load->view("customer/checkout", $data);
				$this->footer();
			}
		}
	}
	
	
	public function current(){
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode('/customer/order'), 'refresh');
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		// fetch open order basket
		$open_basket = $this->getOrCreateOpenBasket($user_id);
		
		$this->basket($open_basket->id);
	}
	
	
	public function index()
	{
		$this->current();
	}
}
