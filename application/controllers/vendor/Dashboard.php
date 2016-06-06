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
		
		// get orders
		$unsorted_orders = $this->order_model->getPaidOrdersForVendor($user_id, $is_filled);
		$sorted_orders = array();
		foreach ($unsorted_orders as $order){
			$sorted_orders[] = $order;
		}
		
		// bind data
		$data["orders"] = $sorted_orders;
		
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
