<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Food extends Yumbox_Controller {

	/**
	 * AJAX method for creating a new dish
	 * echo json string:
	 *   {success, li_display, error}
	 */
	public function new_food(){
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
		$food_name = $this->input->post("name");
		$food_alt_name = $this->input->post("alt_name");
		$food_price = $this->input->post("price");
		
		// create new food
		$food_id = false;
		try {
			$food_id = $this->food_model->createFood($user_id, $food_name, $food_alt_name, $food_price);
		} catch (Exception $e){
			$json_arr["error"] = $e->getMessage();
			echo json_encode($json_arr);
			return;
		}
		
		// get food info
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		// show predicted pickup time
		$pickup_time = $this->time_prediction->calcPickupTime($food->food_id, time(), true);
		$food->prep_time = prep_time_for_display($pickup_time);
		
		// get new element output
		$food_data["food"] = $food;
		$food_data["is_my_profile"] = true;
		$food_item_display = $this->load->view("food_list/food_list_item", $food_data, true);
		
		// success
		$json_arr["success"] = "1";
		$json_arr["li_display"] = $food_item_display;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for removing a dish
	 * echo json string:
	 *   {success, error}
	 */
	public function remove_food($food_id=false){
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
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->vendor_id != $user_id){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		
		// remove food
		$res = $this->food_model->removeFood($food_id);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for changing the name of a dish
	 * echo json string:
	 *   {success, error}
	 */
	public function change_name($food_id=false){
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
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->vendor_id != $user_id){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		
		// change price
		$name = trim($this->input->post("value"));
		if ($name == ""){
			$json_arr["error"] = "name cannot be blank";
			echo json_encode($json_arr);
			return;
		}
		$res = $this->food_model->changeName($food_id, $name);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for changing the name of a dish
	 * echo json string:
	 *   {success, error}
	 */
	public function change_altname($food_id=false){
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
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->vendor_id != $user_id){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		
		// change price
		$name = trim($this->input->post("value"));
		$res = $this->food_model->changeAlternateName($food_id, $name);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for changing the price of a dish
	 * echo json string:
	 *   {success, error}
	 */
	public function change_price($food_id=false){
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
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->vendor_id != $user_id){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		
		// change price
		$price = $this->input->post("value");
		if (!is_numeric($price) || $price < 0){
			$json_arr["error"] = "must be a positive number";
			echo json_encode($json_arr);
			return;
		}
		$res = $this->food_model->changePrice($food_id, $price);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for changing the description of a dish
	 * echo json string:
	 *   {success, error}
	 */
	public function change_description($food_id=false){
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
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->vendor_id != $user_id){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		
		// change price
		$descr = trim($this->input->post("value"));
		$res = $this->food_model->changeDescription($food_id, $descr);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for changing the description of a dish
	 * echo json string:
	 *   {success, error}
	 */
	public function change_ingredients($food_id=false){
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
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->vendor_id != $user_id){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		
		// change price
		$ingredients = trim($this->input->post("value"));
		$res = $this->food_model->changeIngredients($food_id, $ingredients);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for changing the description of a dish
	 * echo json string:
	 *   {success, error}
	 */
	public function change_benefits($food_id=false){
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
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->vendor_id != $user_id){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		
		// change price
		$benefits = trim($this->input->post("value"));
		$res = $this->food_model->changeHealthBenefits($food_id, $benefits);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for changing the description of a dish
	 * echo json string:
	 *   {success, error}
	 */
	public function change_instructions($food_id=false){
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
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->vendor_id != $user_id){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		
		// change price
		$instructions = trim($this->input->post("value"));
		$res = $this->food_model->changeEatingInstructions($food_id, $instructions);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
}
