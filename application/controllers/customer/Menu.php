<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Yumbox_Controller {
	public static $LIST_VIEW = "list";
	public static $MAP_VIEW = "map";
	
	public static $MAX_RESULTS = 4;
	
	/**
	 * Get the data required for the menu filter component for the view
	 * @return an array of data to be passed to view
	 */
	protected function dataForMenuFilter($is_rush, $is_list, $search_query, 
		array $chosen_categories, array $price_filter, array $rating_filter, $time_filter){
		// load language
		$this->lang->load("landing");
		
		// get main food categories
		$main_categories = $this->food_category_model->getAllMainCategories();

		// bind model data
		$data["quick_menu_text"] = $this->lang->line("quick_menu_text");
		$data["full_menu_text"] = $this->lang->line("full_menu_text");
		$data['main_categories'] = $main_categories;
		
		// bind user data
		$data['is_rush'] = $is_rush;
		$data['is_list'] = $is_list;
		$data['chosen_categories'] = $chosen_categories;
		$data['search_query'] = $search_query;
		$data['price_filter'] = $price_filter;
		$data['rating_filter'] = $rating_filter;
		$data['time_filter'] = $time_filter;
		
		return $data;
	}
	
	
	/**
	 * Get the data required for food listing in the view
	 * @return: an array with "foods" => array of foods and "categories" => array of categories
	 */
	protected function dataForFoodListing($is_rush, $search_query, array $chosen_categories){
		// now
		if ($is_rush)
			$now = new DateTime();
		else
			$now = NULL;
		
		// search for foods with the chosen categories
		$foods = array();
		if (count($chosen_categories)==0){
			// show everything
			
			// get all categories
			$categories = $this->food_category_model->getAllActiveCategories($now);
			
			// get all foods
			foreach ($categories as $category){
				$foods[$category->id] = $this->food_model->getActiveFoodsAndVendorWithPicturesForCategory($category->id, self::$MAX_RESULTS, $now);
			}
		} else {
			// show selected
			
			// get all related categories
			$categories = $this->food_category_model->getAllActiveRelatedCategories($chosen_categories, $now);
		
			// get all foods
			foreach ($categories as $category){
				$foods[$category->id] = $this->food_model->getActiveFoodsAndVendorWithPicturesForCategory($category->id, self::$MAX_RESULTS, $now);
			}
		}
		
		// array to be returned
		$ret = array(
			"foods" => $foods,
			"categories" => $categories
		);
		return $ret;
	}
	
	public function fullmenu($view="list"){
		// get user inputs
		$search_query = $this->input->get('search', true);
		$chosen_categories = $this->input->get('category', true);
		if ($chosen_categories==NULL)
			$chosen_categories = array();
		$price_filter = array(
			"min"=>$this->input->get('price_min', true)==""?0:$this->input->get('price_min', true), 
			"max"=>$this->input->get('price_max', true)==""?50:$this->input->get('price_max', true)
		);
		$rating_filter = array(
			"min"=>$this->input->get('rating_min', true)==""?0:$this->input->get('rating_min', true), 
			"max"=>$this->input->get('rating_max', true)==""?5:$this->input->get('rating_max', true)
		);
		$time_filter = $this->input->get('time_max', true)==""?5:$this->input->get('time_max', true);
		
		// fetch data
		if ($view==self::$MAP_VIEW){
			// map view
			
			
		} else {
			// list view
			
			$foods_and_cats = $this->dataForFoodListing(false, $search_query, $chosen_categories);
			$foods = $foods_and_cats["foods"];
			$categories = $foods_and_cats["categories"];
		}
		
		// language
		$this->lang->load("menu");
		
		// bind to data
		$filter_data = $this->dataForMenuFilter(false, $view!=self::$MAP_VIEW, $search_query, 
			$chosen_categories, $price_filter, $rating_filter, $time_filter);
		$data['foods'] = $foods;
		$data['categories'] = $categories;
		$data['empty_string'] = $this->lang->line("no_result");

		// Load views
		$this->header();
		$this->navigation();
		$this->load->view("customer/menu_filter", $filter_data);
		if ($view == self::$MAP_VIEW)
			$this->load->view("customer/map", $data);
		else
			$this->load->view("customer/menu", $data);
		$this->footer();
	}
	
	public function quickmenu($view="map"){
		// get user inputs
		$search_query = $this->input->get('search', true);
		$chosen_categories = $this->input->get('category', true);
		if ($chosen_categories==NULL)
			$chosen_categories = array();
		$price_filter = array(
			"min"=>$this->input->get('price_min', true)==""?0:$this->input->get('price_min', true), 
			"max"=>$this->input->get('price_max', true)==""?50:$this->input->get('price_max', true)
		);
		$rating_filter = array(
			"min"=>$this->input->get('rating_min', true)==""?0:$this->input->get('rating_min', true), 
			"max"=>$this->input->get('rating_max', true)==""?5:$this->input->get('rating_max', true)
		);
		$time_filter = $this->input->get('time_max', true)==""?5:$this->input->get('time_max', true);
		
		// fetch data
		if ($view==self::$MAP_VIEW){
			// map view
			
			
		} else {
			// list view
			
			$foods_and_cats = $this->dataForFoodListing(true, $search_query, $chosen_categories);
			$foods = $foods_and_cats["foods"];
			$categories = $foods_and_cats["categories"];
		}
		
		// bind to data
		$filter_data = $this->dataForMenuFilter(true, $view==self::$LIST_VIEW, $search_query, 
			$chosen_categories, $price_filter, $rating_filter, $time_filter);
		$data['foods'] = $foods;
		$data['categories'] = $categories;

		// Load views
		$this->header();
		$this->navigation();
		$this->load->view("customer/menu_filter", $filter_data);
		if ($view == self::$LIST_VIEW)
			$this->load->view("customer/menu", $data);
		else
			$this->load->view("customer/map", $data);
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
	
	public function item($food_id){
		// get food data
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food == NULL){
			show_404();
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
