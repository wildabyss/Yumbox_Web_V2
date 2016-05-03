<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Yumbox_Controller {

	public function id($id){
		// fetch user
		$user = $this->user_model->getUserForUserId($id);
		if ($user===false){
			show_404();
		}
		
		// is this my profile?
		$my_id = $this->login_util->getUserId();
		if ($my_id !== false && $my_id == $id){
			// my profile
			$myprofile = true;
		} else {
			$myprofile = false;
		}
		
		// get food data
		$categories = $this->food_category_model->getAllActiveCategories(NULL, $user->id);
		$foods = array();
		foreach ($categories as $category){
			$foods[$category->id] = $this->food_model->getActiveFoodsAndVendorWithPicturesForCategory($category->id, self::$MAX_RESULTS, NULL, $user->id);
		}
		
		// language
		$this->lang->load("menu");
		
		
		// bind data
		$data['is_my_profile'] = $myprofile;
		$data['user_name'] = $user->name;
		$data['user_descr'] = $user->descr;
		$data['empty_string'] = $this->lang->line("empty_kitchen");
		$data['foods'] = $foods;
		$data['categories'] = $categories;
		
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
