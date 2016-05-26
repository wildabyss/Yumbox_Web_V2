<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search {
	public static $DEFAULT_COORDS = [43.6532, -79.3832];
	public static $DEFAULT_SEARCH = "Toronto, Ontario";
	
	// location based search radius
	public static $SEARCH_RADIUS = 25;		// 25KM radius for search results
	public static $EARTH_RADIUS = 6371;
	
	// maximum number of results per pagination
	public static $MAX_CATEGORIES_PAGE = 5;
	public static $MAX_FOODS_PAGE = 4;
	public static $MAX_FOODS_PAGE_NO_CATEGORIES = 20;
	
	public function geocodeLocation($location_str){
		// load helper
		$CI =& get_instance();
		$CI->load->helper('utils');
		
		// map request url
		
	}
	
	
	/** 
	 * Get saved user coordinates from datastore
	 * @return [latitude, longitude]
	 */
	public function getUserCoordinates($user_id){
		$this->load->helper('cookie');
		
		// attempt to retrieve location from cookies
		$latitude = get_cookie("latitude");
		$longitude = get_cookie("longitude");
		
		if ($latitude == NULL || $longitude == NULL){
			// attempt to get from user address
			$CI =& get_instance();
			$CI->load->model('user');
			$address = $CI->user_model->getOrCreateAddress($user_id);
			if ($address->longitude == "" || $address->latitude == ""){
				// use default
				return self::$DEFAULT_COORDS;
			} else {
				return array(
					"latitude" => $address->latitude,
					"longitude" => $address->longitude
				);
			}
		} else {
			return array(
				"latitude" => $latitude,
				"longitude" => $longitude
			);
		}
	}
	
	
	/**
	 * From search query, filter food names
	 *
	 * @param string $search_query
	 * @param array $filters
	 * @param bool $show_by_categories
	 * @return [foods, categories]
	 */
	public function searchForFood($search_query, $filters=array(), $show_by_categories=true){
		$search_query = trim($search_query);

		$fulltext_results = array();
		// perform Sphinx full-text search
		if ($search_query != ""){
			$sphinxdb = new mysqli("0", "", "", "", 9306);
			if (!$sphinxdb->connect_errno){
				$ultimate_number = 500000;
			
				// escape search query
				$search_query_filt = $sphinxdb->real_escape_string($search_query);
			
				// form sql query
				$sql_query = <<<EOT
					select 
						id
					from
						food_name_index
					where
						match('$search_query_filt')
					limit
						$ultimate_number
					option
						max_matches = $ultimate_number
EOT;

				// fetch results
				if ($sphinx_res = $sphinxdb->query($sql_query)){
					while ($row_assoc = $sphinx_res->fetch_assoc()){
						$fulltext_results[] = $row_assoc["id"];
					}
					$sphinx_res->close();

					// add to filters
					$filters["food_ids"] = $fulltext_results;
				} else {
					throw new Exception("Sphinx query failed");
				}
			
				// close Sphinx engine
				$sphinxdb->close();
			} else {
				throw new Exception("Cannot connect to Sphihx");
			}
		}
		
		
		// load model
		$CI =& get_instance();
		$CI->load->model('food_model');
		$CI->load->model('food_category_model');
		
		// result containers
		$foods = array();
		$categories = array();
		
		// find all categories
		if (isset($filters["category_ids"]) && count($filters["category_ids"])>0){
			$categories = $CI->food_category_model->getAllActiveRelatedCategories($filters["category_ids"], self::$MAX_CATEGORIES_PAGE, $filters);
		} else {
			$categories = $CI->food_category_model->getAllActiveCategories(self::$MAX_CATEGORIES_PAGE, $filters);
		}
		
		// find all foods
		if ($show_by_categories){
			// get all foods for each category
			foreach ($categories as $category){
				$filters["category_ids"] = [$category->id];
				$foods[$category->id] = $CI->food_model->getActiveFoodsAndVendorAndOrdersAndRatingAndPictures(self::$MAX_FOODS_PAGE, $filters);
			}
		} else {
			// get all foods
			$foods = $CI->food_model->getActiveFoodsAndVendorAndOrdersAndRatingAndPictures(self::$MAX_FOODS_PAGE_NO_CATEGORIES, $filters);
		}
		
		// array to be returned
		$ret = array(
			"foods" => $foods,
			"categories" => $categories
		);
		return $ret;
	}
}
