<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Yumbox_Controller {
	public static $MAX_RESULTS = 50;

	/**
	 * GET method for displaying a user's profile
	 */
	public function id($user_id=NULL){
		// fetch user
		$user = $this->user_model->getUserForUserId($user_id);
		if ($user===false){
			show_404();
		}
		$filters["vendor_id"] = $user->id;
		$filters["is_rush"] = false;
		
		// is this my profile?
		$my_id = $this->login_util->getUserId();
		
		if ($my_id !== false && $my_id == $user_id){
			// my profile
			$myprofile = true;
		} else {
			$myprofile = false;
		}
		
		// get food data for display
		$food_list_display = $this->load->view("food_list/food_list_start", [], true);
		$foods = $this->food_model->
			getActiveFoodsAndVendorAndOrdersAndRatingAndPictures(self::$MAX_RESULTS, $filters);
		$categories = array();
		foreach ($foods as $food){
			$categories[$food->food_id] = $this->food_category_model->getAllCategoriesForFood($food->food_id);
			
			// massage food data for display
			if ($food->total_orders=="")
				$food->total_orders=0;
			
			// massage time for display
			$food->prep_time = prep_time_for_display($food->prep_time);
			
			// parse data for display
			$food_data['categories'] = $categories;
			$food_data["food"] = $food;
			$food_data["is_my_profile"] = $myprofile;
			$food_list_display .= $this->load->view("food_list/food_list_item", $food_data, true);
		}
		$food_list_display .= $this->load->view("food_list/food_list_end", ["is_my_profile"=>$myprofile], true);
		
		// get followers
		$num_followers = $this->user_follow_model->getNumberOfActiveFollowersForUser($user_id);
		
		// bind data
		$data['is_my_profile'] = $myprofile;
		$data['user'] = $user;
		$data['foods'] = $foods;
		$data['food_list_display'] = $food_list_display;
		$data['my_id'] = $my_id;
		$data['num_followers'] = $num_followers;
		
		// load view
		$this->header();
		$this->navigation();
		$this->load->view("vendor/profile", $data);
		$this->footer();
	}
	
	
	/**
	 * AJAX method for modifying a user's display name
	 * echo json string:
	 *   {success, error}
	 */
	public function change_username(){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		// check if user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			$json_arr["error"] = "user not logged in";
			echo json_encode($json_arr);
			return;
		}
		
		// get current user id and new name
		$user_id = $this->login_util->getUserId();
		$username = $this->input->post("value");
		
		// modify username
		$res = $this->user_model->modifyUsername($user_id, $username);
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
	 * AJAX method for modifying a user's email
	 * echo json string:
	 *   {success, error}
	 */
	public function change_email(){
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
		$email = $this->input->post("value");
		
		// modify username
		$res = $this->user_model->modifyEmail($user_id, $email);
		if ($res !== true){
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		echo json_encode($json_arr);
	}
	
	
	public function change_address(){
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
		$values = $this->input->post("value");
		$address = isset($values["address"])?$values["address"]:false;
		$city = isset($values["city"])?$values["city"]:false;
		$province = isset($values["province"])?$values["province"]:false;
		$country = isset($values["country"])?$values["country"]:false;
		$postal_code = isset($values["postal_code"])?$values["postal_code"]:false;
		
		// modify address
		
	}

	
	/**
	 * GET method that displays the logged in user's profile page by default
	 */
	public function index()
	{		
		// check if the user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			// redirect to login
			redirect('/login', 'refresh');
		}
		
		// fetch the user id
		$id = $this->login_util->getUserId();
		
		// redirect appropriately
		$this->id($id);
	}
}
