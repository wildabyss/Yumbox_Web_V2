<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Yumbox_Controller {
	/**
	 * Return the display string of a single order item
	 * @param object $order
	 * @return string Display for the order item
	 */
	protected function displayOrderItem($order){
		// get time to deliver
		$order_time = strtotime($order->order_date);
		$phptime = $this->time_prediction->calcPickupTime($order->food_id, $order_time, false);
		$order->prep_time = date("l F j, g:i A", $phptime);
		
		// calculate cost of order
		if ($order->refund_id != ""){
			$base_cost = 0;
			$taxes = 0;
			$front_commission = 0;
			$back_commission = 0;
		} else {
			$costs = $this->accounting->calcPaidOrderItemCosts($order);
			$base_cost = $costs["base_cost"];
			$taxes = $costs["taxes"];
			$front_commission = $costs["commission"];
			$back_commission = $costs["application_share"];
		}
		
		// calculate vendor share
		$total = $base_cost + $taxes + $front_commission - $back_commission;
		$billing = array(
			"taxes"				=> number_format($taxes,2),
			"front_commission"	=> number_format($front_commission,2),
			"back_commission"	=> number_format($back_commission,2),
			"total"				=> number_format($total,2)
		);
		
		$item_data["food_order"] = $order;
		$item_data["billing"] = $billing;
		return $this->load->view("/vendor/dashboard_order_item", $item_data, true);
	}
	
	
	/** 
	 * Display the dashboard based on whether we want TODO or past orders
	 * @param bool $is_filled true = past orders (finished or canceled), false = current orders
	 */
	protected function displayDashboard($is_filled){
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			redirect("/login?redirect=".urlencode("/vendor/dashboard/todo"), 'refresh');
		}
		
		// get logged in user
		$user_id = $this->login_util->getUserId();
		
		// get orders, sorted based on due date
		$unsorted_orders = $this->order_model->getPaidOrdersForVendor($user_id, $is_filled);
		$sorted_orders = array();
		foreach ($unsorted_orders as $order){
			// get time to deliver
			$order_time = strtotime($order->order_date);
			$phptime = $this->time_prediction->calcPickupTime($order->food_id, $order_time, false);
			$order->prep_time = date("l F j, g:i A", $phptime);
			
			// ensure we don't have repeated key
			for (; isset($sorted_orders[$phptime]); $phptime++);
			$sorted_orders[$phptime] = $order;
		}

		if ($is_filled){
			// show most recent first
			krsort($sorted_orders, SORT_NUMERIC);
		} else {
			// show earliest first
			ksort($sorted_orders, SORT_NUMERIC);
		}
		
		// construct order item list for display
		$orders_display = "";
		foreach ($sorted_orders as $order){
			$orders_display .= $this->displayOrderItem($order);
		}
		
		// bind data
		$data["is_current"] = !$is_filled;
		$data["total_orders"] = count($sorted_orders);
		$data["orders_display"] = $orders_display;
		
		$this->header();
		$this->navigation();
		$this->load->view("/vendor/dashboard", $data);
		$this->footer();
	}
	
	
	public function __construct(){
		parent::__construct();
		
		// load accounting library
		$this->load->library("accounting");
	}
	
	
	/**
	 * GET method for displaying the current TODO orders
	 */
	public function todo(){
		$this->displayDashboard(false);
	}
	
	
	/**
	 * GET method for displaying the past orders, including finished and canceled
	 */
	public function finished(){
		$this->displayDashboard(true);
	}
	
	
	/**
	 * GET method that displays the logged in user's dashboard page by default
	 */
	public function index()
	{
		$this->todo();
	}
	
	
	/**
	 * AJAX method for making the current order filled (finished)
	 * echo json string:
	 *   {success, li_display, error}
	 */
	public function finish($order_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();

		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$json_arr["error"] = "user not logged in";
			echo json_encode($json_arr);
			return;
		}
		
		// get current user and data
		$user_id = $this->login_util->getUserId();
		// order
		$food_order = $this->order_model->getFoodOrder($order_id);
		if ($food_order === false || $food_order->vendor_id != $user_id
			|| $food_order->payment_id == "" || $food_order->refund_id != ""){
			$json_arr["error"] = "invalid order";
			echo json_encode($json_arr);
			return;
		}
		
		// set order to finished
		$res = $this->order_model->finishOrder($order_id);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// for display, refresh order
		$food_order = $this->order_model->getFoodOrder($order_id);
		$li_display = $this->displayOrderItem($food_order);
		
		// success
		$json_arr["success"] = "1";
		$json_arr["li_display"] = $li_display;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for fetching a given order item
	 * echo json string:
	 *   {success, li_display, error}
	 */
	public function retrieve_order_item($order_id = false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();

		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$json_arr["error"] = "user not logged in";
			echo json_encode($json_arr);
			return;
		}
		
		// get current user and data
		$user_id = $this->login_util->getUserId();
		// order
		$food_order = $this->order_model->getFoodOrder($order_id);
		if ($food_order === false || $food_order->vendor_id != $user_id
			|| $food_order->payment_id == ""){
			$json_arr["error"] = "invalid order";
			echo json_encode($json_arr);
			return;
		}
		
		// for display
		$li_display = $this->displayOrderItem($food_order);
		
		// success
		$json_arr["success"] = "1";
		$json_arr["li_display"] = $li_display;
		echo json_encode($json_arr);
	}
}
