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
	 * Get order items and vendors information in the specified basket
	 */
	protected function getOrdersInBasket($basket_id, $is_open_basket = false) {
		// get vendor information
		$vendors = $this->order_basket_model->getAllVendorsInBasket($basket_id);
		
		// get foods per vendor
		$foods_orders = array();
		$base_cost = 0;
		$taxes = 0;
		$commission = 0;
		foreach ($vendors as $vendor){
			$foods_orders[$vendor->id] = $this->order_basket_model->getFoodsPerVendorInBasket($basket_id, $vendor->id);

			foreach ($foods_orders[$vendor->id] as $food_order){
				// calculate paid costs
				if (!$is_open_basket && $food_order->refund_id == ""){
					$costs = $this->accounting->calcPaidOrderItemCosts($food_order);
					
					// sum into total
					$base_cost += $costs["base_cost"];
					$commission += $costs["commission"];
					$taxes += $costs["taxes"];
				}
				
				// get pickup time
				if ($is_open_basket)
					$order_time = time();
				else
					$order_time = strtotime($food_order->order_date);
				$food_order->prep_time = date("l F j, g:i A", $this->time_prediction->calcPickupTime($food_order->food_id, $order_time, false));
			}
		}

		// total cost
		$total_cost = $base_cost + $commission + $taxes;

		// bind data
		$data["vendors"] = $vendors;
		$data["foods_orders"] = $foods_orders;
		$data["base_cost"] = $base_cost;
		$data["commission"] = $commission;
		$data["taxes"] = $taxes;
		$data["total_cost"] = $total_cost;

		return $data;
	}
	
	
	/**
	 * Send Email notification for newly created basket
	 */
	protected function sendEmailNewOrders($basket_id){
		// Sending email to the customer and vendors
		$mustache = new Mustache_Engine();

		// Gathering information
		$user_id = $this->login_util->getUserId();
		$user = $this->user_model->getUserForUserId($user_id);
		$basket = $this->getOrdersInBasket($basket_id);
		// flatten array values
		$foods_orders = $basket['foods_orders'];
		$basket['foods_orders'] = array_values($basket['foods_orders']);

		// Loading the email template for the customer
		//TODO: Do we really want to load email templates according to current language?
		$this->lang->load('email');
		$subject = $mustache->render($this->lang->line('customer_invoice_subject'), array(
			'customer' => $user,
			'basket' => $basket,
			'base_url' => $this->config->item('base_url'),
		));
		$body = $mustache->render($this->lang->line('customer_invoice_body'), array(
			'customer' => $user,
			'basket' => $basket,
			'base_url' => $this->config->item('base_url'),
		));

		// Sending email to customer
		$this->load->library('mail_server');
		$this->mail_server->sendFromWebsite($user->email, $user->name, $subject, $body);

		// Sending email to vendor(s)
		foreach ($basket['vendors'] as $v) {
			$subject = $mustache->render($this->lang->line('vendor_invoice_subject'), array(
				'customer' => $user,
				'vendor' => $v,
				'order' => $foods_orders[$v->id],
				'base_url' => $this->config->item('base_url'),
			));
			$body = $mustache->render($this->lang->line('vendor_invoice_body'), array(
				'customer' => $user,
				'vendor' => $v,
				'order' => $foods_orders[$v->id],
				'base_url' => $this->config->item('base_url'),
			));
			$this->mail_server->sendFromWebsite($v->email, $v->name, $subject, $body);
		}
	}
	
	
	/**
	 * Send Email notification for newly canceled order
	 */
	protected function sendEmailCanceledOrder($order, $explanation, $amount){
		// Sending emails to customer and vendor of the food cancelled
		$mustache = new Mustache_Engine();

		// Gathering information
		$user_id = $this->login_util->getUserId();
		$user = $this->user_model->getUserForUserId($user_id);

		// Loading the email template for the customer
		//TODO: Do we really want to load email templates according to current language?
		$this->lang->load('email');
		$subject = $mustache->render($this->lang->line('customer_refund_subject'), array(
			'customer' => $user,
			'order' => $order,
			'explanation' => $explanation,
			'amount' => $amount,
			'base_url' => $this->config->item('base_url'),
		));
		$body = $mustache->render($this->lang->line('customer_refund_body'), array(
			'customer' => $user,
			'order' => $order,
			'explanation' => $explanation,
			'amount' => $amount,
			'base_url' => $this->config->item('base_url'),
		));

		// Sending email to customer
		$this->load->library('mail_server');
		$this->mail_server->sendFromWebsite($user->email, $user->name, $subject, $body);

		// Sending email to vendor(s)
		$vendor = $this->user_model->getUserForUserId($order->vendor_id);
		$subject = $mustache->render($this->lang->line('vendor_refund_subject'), array(
			'customer' => $user,
			'vendor' => $vendor,
			'order' => $order,
			'explanation' => $explanation,
			'amount' => $amount,
			'base_url' => $this->config->item('base_url'),
		));
		$body = $mustache->render($this->lang->line('vendor_refund_body'), array(
			'customer' => $user,
			'vendor' => $vendor,
			'order' => $order,
			'explanation' => $explanation,
			'amount' => $amount,
			'base_url' => $this->config->item('base_url'),
		));
		$this->mail_server->sendFromWebsite($vendor->email, $vendor->name, $subject, $body);
	}
	
	
	public function __construct(){
		parent::__construct();
		
		// load accounting library
		$this->load->library("accounting");
	}
	
	
	/**
	 * GET method for displaying the order cancel page
	 * @param bool $display true=display the cancel page, false=return as a display string
	 */
	public function cancel($order_id=false, $display=true){
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode("/customer/order/cancel/$order_id"), 'refresh');
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		// get food order information
		$food_order = $this->order_model->getFoodOrder($order_id);
		if ($food_order === false || $food_order->payment_id == "" || $food_order->refund_id != ""){
			show_404();
		}
		
		// is this a vendor or buyer cancellation?
		$is_buyer = $food_order->buyer_id==$user_id;
		$is_vendor = $food_order->vendor_id==$user_id;
		if (!$is_buyer && !$is_vendor){
			show_404();
		}
		
		// get vendor information
		$vendor = $this->user_model->getUserForUserId($food_order->vendor_id);
		
		// get payment costs
		$costs = $this->accounting->calcPaidOrderItemCosts($food_order);
		$base_cost = $costs["base_cost"];
		$commission = $costs["commission"];
		$taxes = $costs["taxes"];
		$total_cost = $base_cost + $commission + $taxes;
		
		// bind data
		$data["food_order"] = $food_order;
		$data["vendor"] = $vendor;
		$data["base_cost"] = $base_cost;
		$data["commission"] = $commission;
		$data["taxes"] = $taxes;
		$data["total_cost"] = $total_cost;
		
		// Load views
		if ($display){
			$this->header();
			$this->navigation();
			$this->load->view("customer/order_cancel", $data);
			$this->footer();
		} else {
			return $this->load->view("customer/order_cancel", $data, true);
		}
		
	}

	
	/**
	 * GET method for displaying a particular basket (open or closed) OR displaying the list of historical baskets
	 */
	public function basket($basket_id=false){
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode("/customer/order/basket/$basket_id"), 'refresh');
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		if ($basket_id === false){
			// show the order history
			
			// get paid order baskets
			$order_baskets = $this->order_basket_model->getPaidOrderBasketsForUser($user_id);
			foreach ($order_baskets as $basket){
				// format date
				$date = strtotime($basket->order_date);
				$basket->order_date = date('l F j, Y');
				
				// modify total cost to accomodate refunds
				$basket->total_cost = $this->order_basket_model->getTotalCostInPaidBasket($basket->id);
			}
			
			// bind data
			$data["order_baskets"] = $order_baskets;
			
			// Load views
			$this->header();
			$this->navigation();
			$this->load->view("customer/basket_history", $data);
			$this->footer();
		} else {
			// get a particular order basket
			
			// Fetch the basket with $basket_id for our current user
			// This is a security precaution
			$order_basket = $this->order_basket_model->getOrderBasketForUser($basket_id, $user_id);

			if ($order_basket === false){
				show_404();
			} else {
				// change time
				$date = strtotime($order_basket->order_date);
				$order_basket->order_date = date('l F j, Y');
				
				// is this the open basket?
				$is_open_basket = $order_basket->is_paid==0;

				// Basket data
				$data = $this->getOrdersInBasket($basket_id, $is_open_basket);
				$data["order_basket"] = $order_basket;
				$data["is_open_basket"] = $is_open_basket;

				// total cost
				if ($is_open_basket) {
					$costs = $this->accounting->calcOpenBasketCosts($basket_id);
					$data["base_cost"] = $costs["base_cost"];
					$data["commission"] = $costs["commission"];
					$data["taxes"] = $costs["taxes"];
					$data["total_cost"] =  $costs["base_cost"] + $costs["commission"] + $costs["taxes"];
				}

				// Load views
				$this->header();
				$this->navigation();
				$this->load->view("customer/basket", $data);
				$this->footer();
			}
		}
	}
	
	
	/**
	 * GET method for displaying the current open order basket
	 * Relies on basket() function
	 */
	public function current(){
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode('/customer/order/current'), 'refresh');
		}
		
		// fetch open order basket
		$open_basket = $this->getOpenBasket();
		
		$this->basket($open_basket->id);
	}
	
	
	/**
	 * Default to displaying the current open order basket
	 */
	public function index()
	{
		$this->current();
	}
	
	
	/**
	 * AJAX method
	 * Retrieve the cancel order page
	 */
	public function retrieve_cancel($order_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		$cancel_view = $this->cancel($order_id, false);
		
		$json_arr["success"] = "1";
		$json_arr["view"] = $cancel_view;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method
	 * Add order to the un-paid (open) order_basket
	 * echo json string:
	 *   {success, order_count, enable_order, error}
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

		// fetch current unfilled orders
		$unfilled_orders = $this->order_model->getTotalUnfilledOrdersForFood($food_id);
		if ($unfilled_orders >= $food->quota){
			$json_arr["error"] = "chef cannot take on more orders";
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
		
		// enable more orders?
		$enable_order = ($unfilled_orders+1 < $food->quota);
		
		$json_arr["success"] = "1";
		$json_arr["order_count"] = $total_items;
		$json_arr["enable_order"] = $enable_order;
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
		try{
			$costs = $this->accounting->calcOpenBasketCosts($open_basket->id);
		} catch (Exception $e){
			$json_arr["error"] = "unknown database error";
			echo json_encode($json_arr);
			return;
		}
		$base_cost = $costs["base_cost"];
		$commission = $costs["commission"];
		$taxes = $costs["taxes"];
		$total_cost = $base_cost + $commission + $taxes;
		
		$json_arr["success"] = "1";
		$json_arr["order_count"] = $total_items;
		$json_arr["base_cost"] = $base_cost;
		$json_arr["commission"] = $commission;
		$json_arr["taxes"] = $taxes;
		$json_arr["total_cost"] = $total_cost;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method
	 * Change order quantity
	 * echo json string:
	 *   {success, item_quantity, order_count, total_cost, error}
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
		
		// fetch quantity in current order item
		$current_order = $this->order_model->getFoodOrder($order_id);
		
		// fetch number of items in basket
		$total_items = $this->order_basket_model->getTotalOrdersInBasket($open_basket->id);
		if ($total_items === false){
			$json_arr["error"] = "unknown database error";
			echo json_encode($json_arr);
			return;
		}
		
		// fetch total cost in basket
		try{
			$costs = $this->accounting->calcOpenBasketCosts($open_basket->id);
		} catch (Exception $e){
			$json_arr["error"] = "unknown database error";
			echo json_encode($json_arr);
			return;
		}
		$base_cost = $costs["base_cost"];
		$commission = $costs["commission"];
		$taxes = $costs["taxes"];
		$total_cost = $base_cost + $commission + $taxes;
		
		$json_arr["success"] = "1";
		$json_arr["item_quantity"] = $current_order->quantity;
		$json_arr["order_count"] = $total_items;
		$json_arr["base_cost"] = $base_cost;
		$json_arr["commission"] = $commission;
		$json_arr["taxes"] = $taxes;
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
		
		$this->db->trans_start();
		
		// get current open basket
		$open_basket = $this->getOpenBasket();
		if ($open_basket->id != $basket_id){
			$json_arr["error"] = "open basket does not match";
			echo json_encode($json_arr);
			return;
		}
		
		// fetch all order items in basket
		$order_items = $this->order_basket_model->getAllOrderItemsInBasket($basket_id);
		if (count($order_items)==0){
			$json_arr["error"] = "basket is empty";
			echo json_encode($json_arr);
			return;
		}
		
		// get Stripe token
		$stripe_token = $this->input->post("token");
		$stripe_private_key = $this->config->item("stripe_secret_key");
		Stripe\Stripe::setApiKey($stripe_private_key);
		
		// set up customer
		try {
			$stripe_customer= Stripe\Customer::create(array(
				"source" => $stripe_token
			));
		} catch (Stripe\Error\Card $e){
			$json_arr["error"] = $e->getMessage();
			echo json_encode($json_arr);
			return;
		}
		
		foreach ($order_items as $order_item){
			if ($order_item->payment_id != "")
				continue;
			
			// check if the given food has been deleted, or if the user has become inactive, or if the kitchen is now closed
			if ($order_item->food_status==Food_model::$INACTIVE_FOOD || 
				$order_item->vendor_status==User_model::$INACTIVE_USER || $order_item->is_open==0){
				
				// remove it from basket
				$this->order_basket_model->removeOrderFromBasket($order_item->order_id, $basket_id);
				
				continue;
			}
			
			// get amount to be charged in dollars
			$costs = $this->accounting->calcOpenOrderItemCosts($order_item);
			$amount = $costs["base_cost"] + $costs["commission"] + $costs["taxes"];
			$shares = $this->accounting->calcOpenOrderItemVendorShare($amount);
			$vendor_share = $shares["vendor_share"];
			$application_share = $shares["application_share"];
			
			// Get vendor information and calculate his share
			$vendor = $this->user_model->getUserForUserId($order_item->vendor_id);
			$amount_in_cents = round($amount * 100);
			$application_share_in_cents = round($application_share * 100);
			
			// charge Stripe
			try {
				$charge = Stripe\Charge::create(array(
					"amount"			=> $amount_in_cents,	// amount in cents
					"currency"			=> "cad",
					"customer"			=> $stripe_customer->id,
					"metadata"			=> array("order_item " => $order_item->order_id),
					"destination"		=> $vendor->stripe_managed_account_id,
					"application_fee"	=> $application_share_in_cents,
				));
			} catch (Stripe\Error\Card $e){
				$json_arr["error"] = $e->getMessage();
				echo json_encode($json_arr);
				return;
			}
			
			// save entry to database
			$rates = $this->accounting->getCurrentRates();
			// save payment info
			$res = $this->payment_model->payOrderItem($amount, $rates['take_rate'], $rates['tax_rate'],
				$order_item->order_id, $charge->id);
			// save payout info
			$res = $res && $this->payment_model->savePayout($vendor_share, $rates["vendor_take_rate"], $order_item->order_id);

			if ($res !== true){
				$json_arr["error"] = $res;
				echo json_encode($json_arr);
				return;
			}
		}

		// change basket status
		$res = $this->order_basket_model->setBasketAsPaid($basket_id);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		$this->db->trans_complete();
		
		// at this point, the orders have been successfully placed
		$json_arr["success"] = "1";
		$json_arr["basket_id"] = $basket_id;
		echo json_encode($json_arr);

		// send notification emails
		$this->sendEmailNewOrders($basket_id);
	}
	
	
	/**
	 * AJAX method
	 * Issue refund on order
	 * echo json string:
	 *   {success, basket_id, error}
	 */
	public function refund($order_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$json_arr["error"] = "user not logged in";
			echo json_encode($json_arr);
			return;
		}
		$user_id = $this->login_util->getUserId();
		
		// get order_item
		$order = $this->order_model->getFoodOrder($order_id);
		if ($order === false){
			$json_arr["error"] = "non-existent order";
			echo json_encode($json_arr);
			return;
		} elseif ($order->refund_id != ""){
			$json_arr["error"] = "already refunded";
			echo json_encode($json_arr);
			return;
		} elseif ($order->payment_id == ""){
			$json_arr["error"] = "order hasn't been paid";
			echo json_encode($json_arr);
			return;
		}
		
		// check buyer or seller
		if ($order->buyer_id==$user_id)
			$refund_type = Refund_model::$TYPE_BUYER;
		elseif ($order->vendor_id==$user_id)
			$refund_type = Refund_model::$TYPE_VENDOR;
		else
			$refund_type = false;
		if ($refund_type === false){
			$json_arr["error"] = "order does not match";
			echo json_encode($json_arr);
			return;
		}
		
		// get payment information
		$payment = $this->payment_model->getPayment($order->payment_id);
		$amount = $payment->amount;
		
		// refund Stripe
		$stripe_private_key = $this->config->item("stripe_secret_key");
		Stripe\Stripe::setApiKey($stripe_private_key);
		try {
			$refund = Stripe\Refund::create(array(
				"charge"		=> $payment->stripe_charge_id,
				"refund_application_fee" => true,
				"reverse_transfer" => true,
			));
		} catch (Stripe\Error\InvalidRequest $e){
			$json_arr["error"] = $e->getMessage();
			echo json_encode($json_arr);
			return;
		}
		
		// retrieve explanation
		$explanation = $this->input->post("explanation");
		
		// save entry to database
		$res = $this->refund_model->refundOrderItem($amount, $order_id, $refund->id, $refund_type, $explanation);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// at this point, the refund is sucessful
		$json_arr["success"] = "1";
		$json_arr["basket_id"] = $order->order_basket_id;
		echo json_encode($json_arr);
		
		// send notification email
		$this->sendEmailCanceledOrder($order, $explanation, $amount);
	}
}
