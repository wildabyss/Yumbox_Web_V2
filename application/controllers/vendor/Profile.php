<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Yumbox_Controller {
	public static $MAX_RESULTS = 50;

	
	/**
	 * GET method that displays the logged in user's profile page by default
	 */
	public function index()
	{
		// check if the user has logged in
		if (!$this->login_util->isUserLoggedIn()){
			// redirect to login
			redirect('/login?redirect='.urlencode("/vendor/profile"), 'refresh');
		}
		
		// fetch the user id
		$id = $this->login_util->getUserId();
		
		// redirect appropriately
		$this->id($id);
	}
	
	
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
		$filters["is_open"] = 0;
		
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
		
		// get user picture
		$user_picture = $this->user_model->getUserPicture($user_id);
		
		// bind data
		$data['is_my_profile'] = $myprofile;
		$data['user'] = $user;
		$data['user_picture'] = $user_picture;
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
	 * AJAX method for modifying a user's description (intro)
	 * echo json string:
	 *   {success, error}
	 */
	public function change_userdescr(){
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
		$descr = $this->input->post("value");
		
		// modify username
		$res = $this->user_model->modifyUserDescription($user_id, $descr);
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
	
	
	/**
	 * AJAX method for modifying a user's address
	 * echo json string:
	 *   {success, error}
	 */
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
		$res = $this->user_model->modifyAddress($user_id, $address, $city, $province, $country, $postal_code);
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
	 * AJAX method for modifying a user's display picture
	 * echo json string:
	 *   {success, filepath, error}
	 */
	public function change_displaypic(){
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
		
		// check if directory exists
		$upload_dir = $_SERVER['DOCUMENT_ROOT'].$this->config->item('user_pics');
		if (!file_exists($upload_dir)){
			mkdir($upload_dir);
		}
		
		// file upload class
		$params['upload_path']          = $upload_dir;
		$params['allowed_types']        = 'jpeg|jpg|png';
		$params['max_size']             = 10000;
		$params['file_name']			= $user_id."_".time();
		$params['overwrite']			= true;
		$this->load->library('upload', $params);

		// upload photo
		if (!$this->upload->do_upload('photo')){
			// error
			$json_arr["error"] = $this->upload->display_errors('','');
			echo json_encode($json_arr);
			return;
		}
		
		// get new file name
		$new_name = $this->upload->data('file_name');

		// get old file path
		$old_path = $this->user_model->getUserPicture($user_id);
		if ($old_path !== false){
			// remove physically
			@unlink($_SERVER['DOCUMENT_ROOT'].$old_path);
		}
		
		// associate new photo in db
		$new_path = $this->config->item('user_pics')."/".$new_name;
		$res = $this->user_model->modifyUserPicture($user_id, $new_path);
		if ($res === false){
			// remove the new file
			delete_files($this->upload->data('full_path'));
			
			$json_arr["error"] = $res;
			echo json_encode($json_arr);
			return;
		}
		
		// success
		$json_arr["success"] = "1";
		$json_arr["filepath"] = $new_path;
		echo json_encode($json_arr);
	}
	
	
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
}
