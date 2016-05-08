<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Yumbox_Controller {
	public static $LIST_VIEW = "list";
	public static $MAP_VIEW = "map";
	
	public static $MAX_RESULTS = 5;
	
	/**
	 * Get the data required for the menu filter component for the view
	 * @return an array of data to be passed to view
	 */
	protected function dataForMenuFilter($is_rush, $is_list, $search_query, 
		array $chosen_categories, $can_deliver, array $price_filter, $rating_filter, $time_filter){
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
		$data['can_deliver'] = $can_deliver;
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
	protected function dataForFoodListing($is_rush, $search_query, 
		array $chosen_categories, $can_deliver, array $price_filter, $rating_filter, $time_filter){
		// filters
		$filters = array();
		$filters["is_rush"] = $is_rush;
		$filters["can_deliver"] = $can_deliver;
		$filters["min_rating"] = $rating_filter;
		$filters["min_price"] = $price_filter["min"];
		$filters["max_price"] = $price_filter["max"];
		$filters["max_time"] = $time_filter;
		
		// search for foods with the chosen categories
		$foods = array();
		if (count($chosen_categories)==0){
			// show all categories
			$categories = $this->food_category_model->getAllActiveCategories($filters);
		} else {
			// show selected categories
			$categories = $this->food_category_model->getAllActiveRelatedCategories($chosen_categories, $filters);
		}
		
		// get all foods for each category
		foreach ($categories as $category){
			$filters_food = $filters;
			$filters_food["category_id"] = $category->id;
			
			$foods[$category->id] = $this->food_model->
				getActiveFoodsAndVendorAndOrdersAndRatingAndPictures(self::$MAX_RESULTS, $filters_food);
		}
		
		// array to be returned
		$ret = array(
			"foods" => $foods,
			"categories" => $categories
		);
		return $ret;
	}
	
	protected function displayMenu($is_rush, $view){
		// load language
		$this->lang->load("menu");
		
		// get user inputs
		$search_query = $this->input->get('search', true);
		$chosen_categories = $this->input->get('category', true);
		if ($chosen_categories==NULL)
			$chosen_categories = array();
		$can_deliver = $this->input->get('can_deliver', true)==""?false:$this->input->get('can_deliver', true);
		$price_filter = array(
			"min"=>$this->input->get('price_min', true)==""?0:$this->input->get('price_min', true), 
			"max"=>$this->input->get('price_max', true)==""?50:$this->input->get('price_max', true)
		);
		$rating_filter = $this->input->get('rating_min', true)==""?0:$this->input->get('rating_min', true);
		$time_filter = $this->input->get('time_max', true)==""?5:$this->input->get('time_max', true);
		
		// fetch data
		if ($view==self::$MAP_VIEW){
			// map view
			
			
		} else {
			// list view
			
			$foods_and_cats = $this->dataForFoodListing($is_rush, $search_query, $chosen_categories, 
				$can_deliver, $price_filter, $rating_filter, $time_filter);
			$foods = $foods_and_cats["foods"];
			$categories = $foods_and_cats["categories"];
		}
		
		// massage food data for display
		foreach ($foods as $cat_id=>$foods_for_cat){
			foreach ($foods_for_cat as $food){
				if ($food->total_orders=="")
					$food->total_orders=0;
				
				$food->prep_time = prepTimeForDisplay($food->prep_time);
			}
		}
		
		// bind to data
		$filter_data = $this->dataForMenuFilter($is_rush, $view!=self::$MAP_VIEW, $search_query, 
			$chosen_categories, $can_deliver, $price_filter, $rating_filter, $time_filter);
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
	
	
	public function fullmenu($view="list"){
		$this->displayMenu(false, $view);
	}
	
	public function quickmenu($view="map"){
		$this->displayMenu(true, $view);
	}

	public function item($food_id){
		// get food data
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food == NULL){
			show_404();
		}
		
		// get food pictures
		$food_pictures = $this->food_model->getFoodPicturesForFoodId($food_id);
		
		// bind to data
		$data['food'] = $food;
		$data['food_pictures'] = $food_pictures;
		
		// Load views
		$this->header();
		$this->navigation();
		$this->load->view("customer/food", $data);
		$this->footer();
	}

	public function index()
	{
		$this->fullmenu();
	}
}
