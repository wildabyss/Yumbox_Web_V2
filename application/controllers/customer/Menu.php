<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Yumbox_Controller {
	public static $LIST_VIEW = "list";
	public static $MAP_VIEW = "map";
	
	// maximum results to show per fetch
	public static $MAX_RESULTS_FOODS = 5;
	public static $MAX_RESULTS_CATEGORIES = 4;
	
	/**
	 * Get the data required for the menu filter component for the view
	 * @return an array of data to be passed to view
	 */
	protected function dataForMenuFilter($is_rush, $is_list, $search_query, $location,
		array $chosen_categories, $can_deliver, array $price_filter, $rating_filter, $time_filter){
		// load language
		$this->lang->load("landing");
		
		// get main food categories
		$main_categories = $this->food_category_model->getAllMainCategories();
		
		// get user location
		if ($location == ""){
			$location = $this->user_model->getUserAddressString($this->login_util->getUserId());
			if ($location === false || $location==""){
				// use default
				$location = Search::$TORONTO_SEARCH;
			}
		}

		// bind model data
		$data["quick_menu_text"] = $this->lang->line("quick_menu_text");
		$data["full_menu_text"] = $this->lang->line("full_menu_text");
		$data['main_categories'] = $main_categories;
		
		// bind user data
		$data['is_rush'] = $is_rush;
		$data['is_list'] = $is_list;
		$data['form_action'] = $is_rush?'/menu/rush':'/menu/explore';
		if ($is_list) 
			$data['form_action'] .= "/list";
		else
			$data['form_action'] .= "/map";
		
		$data["location"] = $location;
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
	protected function dataForFoodListing($is_rush, $search_query, $location,
		array $chosen_categories, $can_deliver, array $price_filter, $rating_filter, $time_filter){
		// filters
		$filters = array();
		$filters["is_rush"] = $is_rush;
		$filters["can_deliver"] = $can_deliver;
		$filters["min_rating"] = $rating_filter;
		$filters["min_price"] = $price_filter["min"];
		$filters["max_price"] = $price_filter["max"];
		$filters["max_time"] = $time_filter;
		$filters["location"] = $location;
		
		// search for foods with the chosen categories
		$foods = array();
		if (count($chosen_categories)==0){
			// show all categories
			$categories = $this->food_category_model->getAllActiveCategories(self::$MAX_RESULTS_CATEGORIES, $filters);
		} else {
			// show selected categories
			$categories = $this->food_category_model->getAllActiveRelatedCategories($chosen_categories, self::$MAX_RESULTS_CATEGORIES, $filters);
		}
		
		// get all foods for each category
		foreach ($categories as $category){
			$filters_food = $filters;
			$filters_food["category_id"] = $category->id;
			
			$foods[$category->id] = $this->food_model->
				getActiveFoodsAndVendorAndOrdersAndRatingAndPictures(self::$MAX_RESULTS_FOODS, $filters_food);
		}
		
		// array to be returned
		$ret = array(
			"foods" => $foods,
			"categories" => $categories
		);
		return $ret;
	}
	
	
	/**
	 * Method for displaying the menu page content
	 * Loads the views directly
	 * @param $view either $LIST_VIEW or $MAP_VIEW
	 */
	protected function displayMenu($is_rush, $view){
		// load language
		$this->lang->load("menu");
		
		// get user inputs
		$search_query = $this->input->get('search');
		$location = $this->input->get('location');
		$chosen_categories = $this->input->get('category');
		if ($chosen_categories==NULL)
			$chosen_categories = array();
		$can_deliver = $this->input->get('can_deliver')==""?false:$this->input->get('can_deliver');
		$price_filter = array(
			"min"=>$this->input->get('price_min')==""?0:$this->input->get('price_min'), 
			"max"=>$this->input->get('price_max')==""?50:$this->input->get('price_max')
		);
		$rating_filter = $this->input->get('rating_min')==""?0:$this->input->get('rating_min');
		$time_filter = $this->input->get('time_max')==""?5:$this->input->get('time_max');
		
		// fetch data
		/*if ($view==self::$MAP_VIEW){
			// map view
			
			
		} else {*/
			// list view
			
			$foods_and_cats = $this->dataForFoodListing($is_rush, $search_query, $location, $chosen_categories, 
				$can_deliver, $price_filter, $rating_filter, $time_filter);
			$foods = $foods_and_cats["foods"];
			$categories = $foods_and_cats["categories"];
		//}
		
		// parse food data for display
		$food_list_display = "";
		foreach ($categories as $category){
			$list_data["category"] = $category;
			$food_list_display .= $this->load->view("food_list/food_list_start", $list_data, true);
			
			foreach ($foods[$category->id] as $food){
				if ($food->total_orders=="")
					$food->total_orders=0;
				
				// show predicted pickup time
				$pickup_time = $this->time_prediction->calcPickupTime($food->food_id, time(), true);
				$food->prep_time = prep_time_for_display($pickup_time);
				
				$food_data["food"] = $food;
				$food_list_display .= $this->load->view("food_list/food_list_item", $food_data, true);
			}
			
			$food_list_display .= $this->load->view("food_list/food_list_end", $list_data, true);
		}
		
		// bind to data
		$filter_data = $this->dataForMenuFilter($is_rush, $view!=self::$MAP_VIEW, $search_query, $location,
			$chosen_categories, $can_deliver, $price_filter, $rating_filter, $time_filter);
		$data["foods"] = $foods;
		$data['food_list_display'] = $food_list_display;
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
	
	
	/**
	 * GET method for displaying the yum explore page
	 * @param $view = $LIST_VIEW or $MAP_VIEW
	 */
	public function explore($view="list"){
		$this->displayMenu(false, $view);
	}
	
	
	/**
	 * GET method for displaying the yum rush page
	 * @param $view = $LIST_VIEW or $MAP_VIEW
	 */
	public function rush($view="map"){
		$this->displayMenu(true, $view);
	}

	
	/**
	 * GET method for displaying a particular food item
	 */
	public function item($food_id=false){
		// check if user has logged in
		if ($this->login_util->isUserLoggedIn()){
			$current_user = $this->login_util->getUserId();
		} else{
			$current_user = false;
		}
		
		// get food data
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false){
			show_404();
		}
		
		// massage food data
		$pickup_time = $this->time_prediction->calcPickupTime($food->food_id, time(), true);
		$food->prep_time = prep_time_for_display($pickup_time);
		
		// get food pictures
		$food_pictures = $this->food_model->getFoodPicturesForFoodId($food_id);
		// for now, grab only one picture
		if (count($food_pictures>1))
			$food_pictures = array_slice($food_pictures, 0, 1);
		
		// get food categories
		$categories = $this->food_category_model->getAllCategoriesForFood($food_id);
		
		// get food reviews
		$reviews = $this->food_review_model->getAllReviewsAndUsersForFood($food_id, 5);
		$user_pictures = array();
		// for each review, get the first user picture
		foreach ($reviews as $review){
			$user_id = $review->user_id;
			$user_pictures[$user_id] = $this->user_model->getUserPicture($user_id);
		}
		
		// bind to data
		$data['food'] = $food;
		$data['food_pictures'] = $food_pictures;
		$data['categories'] = $categories;
		$data['reviews'] = $reviews;
		$data['user_pictures'] = $user_pictures;
		$data['current_user'] = $current_user;
		
		// Load views
		$this->header();
		$this->navigation();
		$this->load->view("customer/food", $data);
		$this->footer();
	}

	/**
	 * Default to displaying the yum explore page
	 */
	public function index()
	{
		$this->explore();
	}
}
