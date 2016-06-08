<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Yumbox_Controller {
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
		$billing = array();
		foreach ($unsorted_orders as $order){
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
			$billing[$order->order_id] = array(
				"taxes"				=> number_format($taxes,2),
				"front_commission"	=> number_format($front_commission,2),
				"back_commission"	=> number_format($back_commission,2),
				"total"				=> number_format($total,2)
			);
			
			$sorted_orders[$phptime] = $order;
		}
		ksort($sorted_orders, SORT_NUMERIC);
		
		// bind data
		$data["foods_orders"] = $sorted_orders;
		$data["billing"] = $billing;
		$data["is_current"] = !$is_filled;
		
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
	
	
	public function todo(){
		$this->displayDashboard(false);
	}
	
	
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
}
