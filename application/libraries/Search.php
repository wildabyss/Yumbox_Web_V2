<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search {
	public static $TORONTO_COORDS = [43.6532, -79.3832];
	public static $TORONTO_SEARCH = "Toronto, Ontario";
	
	// location based search radius
	public static $SEARCH_RADIUS = 25;		// 25KM radius for search results
	public static $EARTH_RADIUS = 6371;
	
	// maximum number of results per pagination
	public static $MAX_CATEGORIES_PAGE = 5;
	public static $MAX_FOODS_PAGE = 5;
	
	public function geocodeLocation($location_str){
		// load helper
		$CI =& get_instance();
		$CI->load->helper('utils');
		
		// map request url
		
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
		// perform Sphinx full-text search
		$fulltext_results = array();
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
					food_name
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
			} else {
				throw new Exception("Sphinx query failed");
			}
			
			// close Sphinx engine
			$sphinxdb->close();
		} else {
			throw new Exception("Cannot connect to Sphihx");
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
			$categories = $this->food_category_model->getAllActiveRelatedCategories($filters["category_ids"], self::$MAX_CATEGORIES_PAGE, $filters);
		} else {
			$categories = $this->food_category_model->getAllActiveCategories(self::$MAX_CATEGORIES_PAGE, $filters);
		}
		
		// find all foods
		if ($show_by_categories){
			// get all foods for each category
			foreach ($categories as $category){
				$filters["category_ids"] = [$category->id];
				$foods[$category->id] = $this->food_model->getActiveFoodsAndVendorAndOrdersAndRatingAndPictures(self::$MAX_RESULTS_FOODS, $filters);
			}
		} else {
			// get all foods
			$foods = $this->food_model->getActiveFoodsAndVendorAndOrdersAndRatingAndPictures(self::$MAX_RESULTS_FOODS, $filters);
		}
		
		// array to be returned
		$ret = array(
			"foods" => $foods,
			"categories" => $categories
		);
		return $ret;
	}
}