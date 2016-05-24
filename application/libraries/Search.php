<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search {
	public static $TORONTO_COORDS = [43.6532, -79.3832];
	public static $TORONTO_SEARCH = "Toronto, Ontario";
	
	public static $SEARCH_RADIUS = 25;		// 25KM radius for search results
	public static $EARTH_RADIUS = 6371;
	
	/**
	 * From search query, filter food names
	 */
	public function searchForFood($search_query, $limit_results, $filters){
		
	}
}