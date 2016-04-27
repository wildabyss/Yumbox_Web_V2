<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Customer_Controller {
	public function fullmenu(){
		// load language
		$this->lang->load("landing");
		
		// get food categories
		$food_categories = $this->food_category_model->getAllActiveCategories();
		
		// get foods per category
		$foods = array();
		foreach ($food_categories as $category){
			$foods[$category->id] = $this->food_model->getActiveFoodsWithPicturesForCategory($category->id, 5);
		}
		
		// bind to data
		$data["quick_menu_text"] = $this->lang->line("quick_menu_text");
		$data["full_menu_text"] = $this->lang->line("full_menu_text");
		$data['is_rush'] = false;
		$data['food_categories'] = $food_categories;
		$data['foods'] = $foods;

		// Load views
		$this->header();
		$this->navigation();
		$this->load->view("customer/menu", $data);
		$this->footer();
	}
	
	public function quickmenu(){
		// load language
		$this->lang->load("landing");
		
		// now
		$now = new DateTime();
		
		// get food categories
		$food_categories = $this->food_category_model->getAllQuickFoodCategories($now);
		
		// get foods per category
		$foods = array();
		foreach ($food_categories as $category){
			$foods[$category->id] = $this->food_model->getQuickFoodsWithPicturesForCategory($category->id, $now, 5);
		}
		
		// bind to data
		$data["quick_menu_text"] = $this->lang->line("quick_menu_text");
		$data["full_menu_text"] = $this->lang->line("full_menu_text");
		$data['is_rush'] = true;
		$data['food_categories'] = $food_categories;
		$data['foods'] = $foods;

		// Load views
		$this->header();
		$this->navigation();
		$this->load->view("customer/menu", $data);
		$this->footer();
	}
	
	public function food($food_id=0){
		// get food data
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food == NULL){
			redirect('/landing', 'refresh');
		}
		
		// get food pictures
		$food_pictures = $this->food_model->getFoodPicturesForFoodId($food_id);
		
		// cutoff time
		$bool_past_cutoff = false;
		if ($food->cutoff_time == '00:00:00'){
			$food->cutoff_time = 'All Day';
		} else {
			$cutoff_time = new DateTime($food->cutoff_time);
			$cutoff_time->modify('-'.Food_model::$CUTOFF_GRACE_MIN.' minutes');
			$food->cutoff_time = $cutoff_time->format("H:i:s");
			
			$now = new DateTime();
			if ($now->format("H:i:s") > $food->cutoff_time)
				$bool_past_cutoff = true;
		}
		
		// bind to data
		$data['food'] = $food;
		$data['food_pictures'] = $food_pictures;
		$data['bool_past_cutoff'] = $bool_past_cutoff;
		
		// Load views
		$this->header();
		$this->load->view("customer/food", $data);
		$this->footer();
	}

	public function index()
	{
		$this->fullmenu();
	}
}
