<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Yumbox_Controller {
	/**
	 * Return the open basket for the current user
	 * This method assumes that the login exists and is valid
	 */
	protected function getOpenBasket(){
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		// fetch open order basket
		$open_basket = $this->order_basket_model->getOrCreateOpenBasket($user_id);
		
		return $open_basket;
	}
	
	
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

		// fetch open order basket
		$open_basket = $this->getOpenBasket();
		
		// add order to this basket
		$res = $this->order_basket_model->addOrderToBasket($food_id, $open_basket->id);
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
	 *   {success, order_count, total_cost, error}
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
		
		// fetch open order basket
		$open_basket = $this->getOpenBasket();
		
		// delete order from open basket
		$res = $this->order_basket_model->removeOrderFromBasket($order_id, $open_basket->id);
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
		
		// fetch total cost in basket
		$total_cost = $this->order_basket_model->getTotalCostInBasket($open_basket->id);
		if ($total_cost === false){
			$json_arr["error"] = "unknown database error";
			echo json_encode($json_arr);
			return;
		}
		
		$json_arr["success"] = "1";
		$json_arr["order_count"] = $total_items;
		$json_arr["total_cost"] = $total_cost;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method
	 * Change order quantity
	 * echo json string:
	 *   {success, order_count, total_cost, error}
	 */
	public function change($order_id = false, $quantity = false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		// ensure we have valid quantity
		if ($quantity <= 0){
			$json_arr["error"] = "incorrect quantity";
			echo json_encode($json_arr);
			return;
		}
		
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$json_arr["error"] = "user not logged in";
			echo json_encode($json_arr);
			return;
		}
		
		// fetch open order basket
		$open_basket = $this->getOpenBasket();
		
		// change order quantity
		$res = $this->order_model->changeOrderQuantity($order_id, $open_basket->id, $quantity);
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
		
		// fetch total cost in basket
		$total_cost = $this->order_basket_model->getTotalCostInBasket($open_basket->id);
		if ($total_cost === false){
			$json_arr["error"] = "unknown database error";
			echo json_encode($json_arr);
			return;
		}
		
		$json_arr["success"] = "1";
		$json_arr["order_count"] = $total_items;
		$json_arr["total_cost"] = $total_cost;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method
	 * Make credit card payment
	 * echo json string:
	 *   {success, basket_id, error}
	 */
	public function payment($basket_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$json_arr["error"] = "user not logged in";
			echo json_encode($json_arr);
			return;
		}
		
		// get current open basket
		$open_basket = $this->getOpenBasket();
		if ($open_basket->id != $basket_id){
			$json_arr["error"] = "open basket does not match";
			echo json_encode($json_arr);
			return;
		}
		
		// get total amount
		$amount = $this->order_basket_model->getTotalCostInBasket($open_basket->id);
		$item_count = $this->order_basket_model->getTotalOrdersInBasket($open_basket->id);
		if ($amount <= 0 && $item_count <= 0){
			$json_arr["error"] = "basket empty";
			echo json_encode($json_arr);
			return;
		}
		
		// get Stripe token
		$stripe_token = $this->input->post("token");
		$stripe_private_key = $this->config->item("stripe_secret_key");
		Stripe\Stripe::setApiKey($stripe_private_key);
		
		// charge Stripe
		try {
			$charge = Stripe\Charge::create(array(
				"amount"		=> $amount*100,	// amount in cents
				"currency"		=> "cad",
				"source"		=> $stripe_token,
				"metadata"		=> array("basket_id" => $open_basket->id)
			));
		} catch (Stripe\Error\Card $e){
			$json_arr["error"] = $e->getMessage();
			echo json_encode($json_arr);
			return;
		}
		
		// save entry to database
		$res = $this->payment_model->payOpenBasket($amount, $open_basket->id, $charge->id);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		$json_arr["success"] = "1";
		$json_arr["basket_id"] = $open_basket->id;
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
			$this->load->view("customer/basket");
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
				
				// total cost
				$total_cost = $this->order_basket_model->getTotalCostInBasket($basket_id);
				
				// get vendor information
				$vendors = $this->order_basket_model->getAllVendorsInBasket($basket_id);
				
				// get foods per vendor
				$foods_orders = array();
				foreach ($vendors as $vendor){
					$foods_orders[$vendor->id] = $this->order_basket_model->getFoodsPerVendorInBasket($basket_id, $vendor->id);
				}

				// bind data
				$data["order_basket"] = $order_basket;
				$data["is_open_basket"] = $is_open_basket;
				$data["vendors"] = $vendors;
				$data["foods_orders"] = $foods_orders;
				$data["total_cost"] = $total_cost;
				
				// Load views
				$this->header();
				$this->navigation();
				$this->load->view("customer/basket", $data);
				$this->footer();
			}
		}
	}
	
	
	public function current(){
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode('/customer/order'), 'refresh');
		}
		
		// fetch open order basket
		$open_basket = $this->getOpenBasket();
		
		$this->basket($open_basket->id);
	}
	
	
	public function index()
	{
		$this->current();
	}
}
