<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Yumbox_Controller {
	public static $MAX_RESULTS = 5;

	public function id($user_id){
		// csrf hash
		$csrf = array(
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->get_csrf_hash()
		);
		
		// fetch user
		$user = $this->user_model->getUserForUserId($user_id);
		if ($user===false){
			show_404();
		}
		$filters["vendor_id"] = $user->id;
		
		// is this my profile?
		$my_id = $this->login_util->getUserId();
		
		if ($my_id !== false && $my_id == $user_id){
			// my profile
			$myprofile = true;
		} else {
			$myprofile = false;
		}
		
		// get food data
		$categories = $this->food_category_model->getAllActiveCategories(false, $user->id);
		$foods = array();
		foreach ($categories as $category){
			$foods[$category->id] = $this->food_model->
				getActiveFoodsAndVendorAndOrdersAndRatingWithPicturesForCategory($category->id, self::$MAX_RESULTS, $filters);
		}
		
		// get followers
		$num_followers = $this->user_follow_model->getNumberOfActiveFollowersForUser($user_id);
		
		// language
		$this->lang->load("menu");
		
		
		// bind data
		$data['is_my_profile'] = $myprofile;
		$data['user_name'] = $user->name;
		$data['user_descr'] = $user->descr;
		$data['is_open'] = $user->is_open;
		$data['empty_string'] = $this->lang->line("empty_kitchen");
		$data['foods'] = $foods;
		$data['categories'] = $categories;
		$data['my_id'] = $my_id;
		$data['num_followers'] = $num_followers;
		
		// load view
		$this->header();
		$this->navigation();
		$this->load->view("vendor/profile", $data);
		$this->load->view("customer/menu", $data);
		$this->footer();
	}

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
