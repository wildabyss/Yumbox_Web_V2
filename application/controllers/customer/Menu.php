<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Yumbox_Controller {
	public static $LIST_VIEW = "list";
	public static $MAP_VIEW = "map";
	
	/**
	 * Get the data required for the menu filter component for the view
	 * @return an array of data to be passed to view
	 */
	protected function dataForMenuFilter($is_rush, $is_list, $search_query, $location,
		array $chosen_categories, $can_deliver, array $price_filter, $rating_filter){
		// load language
		$this->lang->load("landing");
		$this->lang->load("menu");
		
		// get main food categories
		$main_categories = $this->food_category_model->getAllMainCategories();

		// bind model data
		$data["quick_menu_text"] = $this->lang->line("quick_menu_text");
		$data["full_menu_text"] = $this->lang->line("full_menu_text");
		$data['search_place_holder'] = $this->lang->line("search_place_holder");
		$data['main_categories'] = $main_categories;
		$data['location_btn_label'] = $this->lang->line("location_btn_label");
		$data['filters_btn_label'] = $this->lang->line("filters_btn_label");
		
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
		
		return $data;
	}
	
	
	/**
	 * Return string that contains the views for $foods
	 * If $category provided, assume that $foods is grouped by category->id, otherwise assume that $foods
	 * is a flat array of food objects
	 */
	 protected function displayFoodListing($foods, $is_rush, $category=false){
		if (count($foods)==0)
			return "";
		
		// food category display
		$list_data = array();
		if ($category !== false)
			$list_data["category"] = $category;
		$list_data["is_rush"]	= $is_rush;
		$food_list_display = $this->load->view("food_list/food_list_start", $list_data, true);
		
		// food list display
		foreach ($foods as $food){
			// change for display
			if ($food->total_orders=="")
				$food->total_orders=0;
			
			// show predicted pickup time
			$pickup_time = $this->time_prediction->calcPickupTime($food->food_id, time(), true);
			$food->prep_time = prep_time_for_display($pickup_time);
			$food->food_pic = prep_food_image_filename($food->pic_path);
			
			$food_data["food"] = $food;
			$food_list_display .= $this->load->view("food_list/food_list_item", $food_data, true);
		}
		
		//$list_data["show_more"] = count($foods)>=Search::$MAX_FOODS_PAGE;
		$food_list_display .= $this->load->view("food_list/food_list_end", $list_data, true);
		
		return $food_list_display;
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
		$location_str = $this->input->get('location');
		$chosen_categories = $this->input->get('category');
		if ($chosen_categories==NULL)
			$chosen_categories = array();
		$can_deliver = $this->input->get('can_deliver')==""?false:$this->input->get('can_deliver');
		$price_filter = array(
			"min"=>$this->input->get('price_min')==""?0:$this->input->get('price_min'), 
			"max"=>$this->input->get('price_max')==""?5000:$this->input->get('price_max')
		);
		$rating_filter = $this->input->get('rating_min')==""?0:$this->input->get('rating_min');
	
		// get user location
		$location = false;
		// geocode the input location string
		if ($location_str!=""){
			$location = $this->search->geocodeLocation($location_str);
			
			if ($location !== false){
				setcookie("latitude", $location["latitude"]);
				setcookie("longitude", $location["longitude"]);
			}
		}
		// use fallback location if we cannot identify any input location
		if ($location === false){
			$user_id = $this->login_util->getUserId();
			$location = $this->search->getUserCoordinates($user_id);
			$location_str = "{$location["latitude"]}, {$location["longitude"]}";
		}
	
		// search filters
		$filters = array();
		$filters["is_rush"] = $is_rush;
		$filters["category_ids"] = $chosen_categories;
		$filters["can_deliver"] = $can_deliver;
		$filters["min_rating"] = $rating_filter;
		$filters["min_price"] = $price_filter["min"];
		$filters["max_price"] = $price_filter["max"];
		$filters["location"] = $location;
		// map view defaults to show all results
		if ($view == self::$MAP_VIEW){
			$filters["show_all"] = true;
		}
		$show_by_categories = count($chosen_categories)==0;
		// perform search
		$foods_and_cats = $this->search->searchForFood($search_query, $filters, $show_by_categories);
		$foods = $foods_and_cats["foods"];
		$categories = $foods_and_cats["categories"];
		
		// parse food data for display
		$food_list_display = "";
		if ($show_by_categories){
			foreach ($categories as $category){
				$food_list_display .= $this->displayFoodListing($foods[$category->id], $is_rush, $category);
			}
		} else {
			$food_list_display .= $this->displayFoodListing($foods["all"], $is_rush);
		}
		
		// show more categories?
		//$show_more = count($categories) >= Search::$MAX_CATEGORIES_PAGE && $show_by_categories;
		
		// bind to filter data
		$filter_data = $this->dataForMenuFilter($is_rush, $view!=self::$MAP_VIEW, $search_query, $location_str,
			$chosen_categories, $can_deliver, $price_filter, $rating_filter);

		// Load views
		$this->header();
		$this->navigation();
		$this->load->view("customer/menu_filter", $filter_data);
		
		// load map or list view
		if ($view == self::$MAP_VIEW) {
			// construct the foods_for_map array:
			// [vendor_id => [user object, foods => [food objects]]]
			$foods_for_map = array();
			foreach ($foods as $foods_per_cat) {
				foreach ($foods_per_cat as $food) {
					if (!isset($foods_for_map[$food->vendor_id])) {
						$foods_for_map[$food->vendor_id] = $this->user_model->getUserForUserId($food->vendor_id);
						$foods_for_map[$food->vendor_id]->foods = array();
					}
					
					// we keep the food_id to remove the duplicate foods in different categories
					$foods_for_map[$food->vendor_id]->foods[$food->food_id] = $food;
				}
			}
			
			// flatten the food id indices
			foreach ($foods_for_map as $vendor_id => $data_per_vendor){
				$foods_for_map[$vendor_id]->foods = array_values($foods_for_map[$vendor_id]->foods);
			}
			
			// bind to data
			// flatten the vendor indices
			$map_data['foods_for_map'] = array_values($foods_for_map);
			$map_data['template'] = array("template"=>$this->load->view("customer/map_item", array(), true));
			
			// load view
			$this->load->view("customer/map", $map_data);
		} else {
			// bind to data
			$data["foods"] = $foods;
			$data['food_list_display'] = $food_list_display;
			$data['empty_string'] = $this->lang->line("no_result");
			//$data['show_more'] = $show_more;
			
			// load view
			$this->load->view("customer/menu", $data);
		}
			
		$this->footer();
	}
	
	
	/**
	 * Method for displaying the list of reviews for the given food
	 */
	protected function displayReviews($food_id){
		$review_display = "";
		
		// get food reviews
		$reviews = $this->food_review_model->getAllReviewsAndUsersForFood($food_id, 5);
		// for each review, get the first user picture
		foreach ($reviews as $review){
			$user_id = $review->user_id;
			$user_picture = $this->user_model->getUserPicture($user_id);
			
			$data["review"] = $review;
			$data["user_picture"] = $user_picture;
			
			$review_display .= $this->load->view("customer/review_item", $data, true);
		}
		
		return $review_display;
	}
	
	
	/**
	 * Default to displaying the yum explore page
	 */
	public function index()
	{
		$this->explore();
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
	 * @param bool $display if false, return the food_item view in a string
	 */
	public function item($food_id=false, $display=true){
		// check if user has logged in
		$current_user = false;
		if ($this->login_util->isUserLoggedIn()){
			$current_user_id = $this->login_util->getUserId();
			$current_user = $this->user_model->getUserForUserId($current_user_id);
		}
		
		// get food data
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->food_status == Food_model::$INACTIVE_FOOD || $food->vendor_status == User_model::$INACTIVE_USER){
			show_404();
		}
		
		// does this food belong to the logged in user?
		$is_my_profile = ($current_user !== false && $food->vendor_id == $current_user->id);
		
		// massage food data
		if (!$is_my_profile){
			$pickup_time = $this->time_prediction->calcPickupTime($food->food_id, time(), true);
			$food->prep_time = prep_time_for_display($pickup_time);
		}
		
		// can orders be placed?
		$unfilled_orders = $this->order_model->getTotalUnfilledOrdersForFood($food_id);
		$enable_order = ($food->quota>$unfilled_orders && $food->is_open);
		
		// get food pictures
		$food_pictures = $this->food_model->getFoodPicturesForFoodId($food_id);
		// for now, grab only one picture
		$food_picture = false;
		if (count($food_pictures) > 0) {
			$food_picture = $food_pictures[0];
			$food_picture->path = prep_food_image_filename($food_picture->path);
		}
		
		// get food categories
		$categories = $this->food_category_model->getAllCategoriesForFood($food_id);
		
		// get food reviews
		$review_display = $this->displayReviews($food_id);
		$can_add_review = $this->food_review_model->canUserAddReviewForFood($current_user !== false?$current_user->id:false, $food_id);
		
		// bind to data
		$data['food'] = $food;
		$data['food_picture'] = $food_picture;
		$data['categories'] = $categories;
		$data['review_display'] = $review_display;
		$data['current_user'] = $current_user;
		$data['can_add_review'] = $can_add_review;
		$data['enable_order'] = $enable_order;
		$data['is_my_profile'] = $is_my_profile;
		$data['unfilled_orders'] = $unfilled_orders;
		
		if ($display){
			// Load views
			$this->header();
			$this->navigation();
			$this->load->view("food_list/food", $data);
			$this->footer();
		} else {
			return $this->load->view("food_list/food", $data, true);
		}
	}
	
	
	/**
	 * AJAX method for retrieving the view of a food_item (/food_list/food.php)
	 * echo json string:
	 *   {success, view}
	 */
	public function retrieve_item($food_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		$item_view = $this->item($food_id, false);
		
		$json_arr["success"] = "1";
		$json_arr["view"] = $item_view;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for retrieving the list item view of a food_item (/food_list/food_list_item.php)
	 * echo json string:
	 *   {success, li_display, error}
	 */
	public function retrieve_list_item($food_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		// get current logged in user (could be false if not logged in)
		$user_id = $this->login_util->getUserId();
		
		// get food info
		$food = $this->food_model->getFoodAndVendorForFoodId($food_id);
		if ($food === false || $food->food_status == Food_model::$INACTIVE_FOOD || $food->vendor_status == User_model::$INACTIVE_USER){
			$json_arr["error"] = "incorrect dish specified";
			echo json_encode($json_arr);
			return;
		}
		// show predicted pickup time
		$pickup_time = $this->time_prediction->calcPickupTime($food->food_id, time(), true);
		$food->prep_time = prep_time_for_display($pickup_time);
		$food->food_pic = prep_food_image_filename($food->pic_path);
		
		// get food categories
		$categories[$food_id] = $this->food_category_model->getAllCategoriesForFood($food_id);
		
		// get new element output
		$food_data["food"] = $food;
		$food_data["is_my_profile"] = ($food->vendor_id==$user_id);
		$food_data["categories"] = $categories;
		$food_item_display = $this->load->view("food_list/food_list_item", $food_data, true);
		
		// success
		$json_arr["success"] = "1";
		$json_arr["li_display"] = $food_item_display;
		echo json_encode($json_arr);
	}
	
	
	/**
	 * AJAX method for adding a review for food
	 * echo json string:
	 *   {success, li_display, can_add_more, error}
	 */
	public function add_review($food_id=false){
		// ensure we have POST request
		if (!is_post_request())
			show_404();
		
		// get current logged in user
		$user_id = $this->login_util->getUserId();
		
		// check if user can add review
		if (!$this->food_review_model->canUserAddReviewForFood($user_id, $food_id)){
			$json_arr["error"] = "cannot add review";
			echo json_encode($json_arr);
			return;
		}
		
		// add review
		$rating = $this->input->post("rating");
		$review = $this->input->post("review");
		$review_id = false;
		try {
			$review_id = $this->food_review_model->addReviewForFood($user_id, $food_id, $rating, $review);
		} catch (Exception $e){
			$json_arr["error"] = $e->getMessage();
			echo json_encode($json_arr);
			return;
		}
		
		// retrieve review
		$review = $this->food_review_model->getReviewForId($review_id);
		$user_picture = $this->user_model->getUserPicture($user_id);
		$data["review"] = $review;
		$data["user_picture"] = $user_picture;
		$review_display = $this->load->view("customer/review_item", $data, true);
		
		// can add more reviews?
		$can_add_more = $this->food_review_model->canUserAddReviewForFood($user_id, $food_id);
		
		// success
		$json_arr["success"] = "1";
		$json_arr["li_display"] = $review_display;
		$json_arr["can_add_more"] = $can_add_more;
		echo json_encode($json_arr);
	}

	public function food_pic($file_name)
	{
		$width = (int) $this->input->get('width');
		$height = (int) $this->input->get('height');

		//Original filename and path
		$food_pics = $_SERVER['DOCUMENT_ROOT'] . $this->config->item('food_pics');
		$file_name = $food_pics . DIRECTORY_SEPARATOR . $file_name;

		download_cached_image($file_name, $width, $height);
	}
}
