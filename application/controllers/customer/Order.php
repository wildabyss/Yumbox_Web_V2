<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Yumbox_Controller {
	
	/**
	 * AJAX method
	 * Add order to the un-paid (open) order_basket
	 * echo json string:
	 *   {success, order_count, error}
	 */
	public function add($food_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		// get food data
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false){
			$json_arr["error"] = "food_id $food_id not found";
			echo json_encode($json_arr);
			return;
		}
		
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$json_arr["error"] = "user not logged in";
			echo json_encode($json_arr);
			return;
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		// fetch open order basket
		$open_basket = $this->order_basket_model->getOrCreateOpenBasket($user_id);
		
		// add order to this basket
		$res = $this->order_model->addOrderToBasket($food_id, $open_basket->id);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// fetch number of items in basket
		$total_items = $this->order_basket_model->getTotalOrdersInBasket($open_basket->id);
		if ($total_items === false){
			$json_arr["error"] = "unknown database error";
			echo json_encode($json_arr);
			return;
		}
		
		$json_arr["success"] = "1";
		$json_arr["order_count"] = $total_items;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method
	 * Remove order from the un-paid (open) order_basket
	 * echo json string:
	 *   {success, order_count, error}
	 */
	public function remove($order_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$json_arr["error"] = "user not logged in";
			echo json_encode($json_arr);
			return;
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		// fetch open order basket
		$open_basket = $this->order_basket_model->getOrCreateOpenBasket($user_id);
		
		// delete order from open basket
		$res = $this->order_model->removeOrderFromBasket($order_id, $open_basket->id);
		
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// fetch number of items in basket
		$total_items = $this->order_basket_model->getTotalOrdersInBasket($open_basket->id);
		if ($total_items === false){
			$json_arr["error"] = "unknown database error";
			echo json_encode($json_arr);
			return;
		}
		
		$json_arr["success"] = "1";
		$json_arr["order_count"] = $total_items;
		echo json_encode($json_arr);
	}

	
	public function basket($basket_id=false){
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode('/customer/order'), 'refresh');
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		if ($basket_id === false){
			// show the order history
			
			
			
			// Load views
			$this->header();
			$this->navigation();
			$this->load->view("customer/checkout");
			$this->footer();
		} else {
			// get a particular order basket
			
			// Fetch the basket with $basket_id for our current user
			// This is a security precaution
			$order_basket = $this->order_basket_model->getOrderBasketForUser($basket_id, $user_id);
			
			if ($order_basket === false){
				show_404();
			} else {
				// is this the open basket?
				$is_open_basket = $order_basket->payment_id =="";
				
				$total_cost = 0;
				
				// get vendor information
				$vendors = $this->order_basket_model->getAllVendorsInBasket($basket_id);
				
				// get foods per vendor
				$foods_orders = array();
				foreach ($vendors as $vendor){
					$foods_orders[$vendor->id] = $this->order_basket_model->getFoodsPerVendorInBasket($basket_id, $vendor->id);
					foreach ($foods_orders[$vendor->id] as $food_order){
						$total_cost += $food_order->quantity*$food_order->price;
					}
				}

				// bind data
				$data["is_open_basket"] = $is_open_basket;
				$data["vendors"] = $vendors;
				$data["foods_orders"] = $foods_orders;
				$data["total_cost"] = $total_cost;
				
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
		$open_basket = $this->order_basket_model->getOrCreateOpenBasket($user_id);
		
		$this->basket($open_basket->id);
	}
	
	
	public function index()
	{
		$this->current();
	}
}
