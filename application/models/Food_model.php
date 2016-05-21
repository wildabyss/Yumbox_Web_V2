<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Food_model extends CI_Model {
	// food status
	public static $INACTIVE_FOOD = 0;
    public static $ACTIVE_FOOD = 1;
	
	// pickup method
	public static $PICKUP_ANYTIME = 0;
	public static $PICKUP_DESIGNATED = 1;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * For a given $categoryId, fetch all the foods with its pictures up to $limit
	 * Fetch also its total number of orders and its aggregate rating
	 *
	 * @param $filters:
	 *    is_rush		=> bool
	 *    category_id   => int
	 *    can_deliver   => bool
	 *    vendor_id     => int
	 *    min_rating	=> int
	 *    min_price     => float
	 *    max_price     => float
	 */
	public function getActiveFoodsAndVendorAndOrdersAndRatingAndPictures($limit, array $filters){
		
		// sort through filters
		$is_rush = isset($filters["is_rush"])?$filters["is_rush"]:false;
		$categoryId = isset($filters["category_id"])?$filters["category_id"]:false;
		$can_deliver = isset($filters["can_deliver"])?$filters["can_deliver"]:false;
		$vendor_id = isset($filters["vendor_id"])?$filters["vendor_id"]:false;
		$min_rating = isset($filters["min_rating"])?$filters["min_rating"]:false;
		$min_price = isset($filters["min_price"])?$filters["min_price"]:false;
		$max_price = isset($filters["max_price"])?$filters["max_price"]:false;
		
		// base query
		$query_str = '
			select 
				f.id food_id, f.name food_name, f.alternate_name food_alt_name, 
				f.price food_price, f.pickup_method, f.prep_time_hours prep_time,
				average_rating(f.id)/?*100 rating,
				u.is_open,
				p.path pic_path,
				total_orders(f.id) total_orders,
				u.id vendor_id, u.name vendor_name
			from food_category_assoc a
			left join food f
			on f.id = a.food_id
			left join user u
			on u.id = f.user_id
			left join food_picture p
			on p.food_id = f.id
			where
				f.status = ?
				and u.status = ?
				and u.is_open = 1';
		
		// filter out non-rush items
		if ($is_rush){
			$query_str .= ' and f.pickup_method = ? and f.prep_time_hours <= ?';
		} 
		// filter selected category
		if ($categoryId !== false){
			$query_str .= ' and a.food_category_id = ?';
		}
		// filter minimum rating
		if ($min_rating !== false){
			$query_str .= ' and average_rating(f.id) >= ?';
		}
		// filter prices
		if ($min_price !== false){
			$query_str .= ' and f.price >= ? and f.price <= ?';
		}
		// filter user
		if ($vendor_id !== false){
			$query_str .= ' and u.id = ?';
		}
		$query_str .= ' group by f.id limit ?';
		
		// bindings
		$bindings = array(
			Food_review_model::$HIGHEST_RATING,
			Food_model::$ACTIVE_FOOD, 
			User_model::$CERTIFIED_VENDOR
		);
		if ($is_rush){
			$bindings[] = Food_model::$PICKUP_ANYTIME;
			$bindings[] = Time_prediction::$RUSH_HOUR_CUTOFF;
		}
		if ($categoryId !== false){
			$bindings[] = $categoryId;
		}
		if ($min_rating !== false){
			$bindings[] = $min_rating;
		}
		if ($min_price !== false){
			$bindings[] = $min_price;
			$bindings[] = $max_price;
		}
		if ($vendor_id !== false){
			$bindings[] = $vendor_id;
		}
		$bindings[] = $limit;
		
		// perform database query
		$query = $this->db->query($query_str, $bindings);
		return $query->result();
	}
	
	
	/**
	 * Get food and vendor information given the food id
	 * Only select food whose status is active and whose vendor is certified
	 *
	 * @param: $food_id
	 * @return: an object with food and vendor info, or false if unavailable
	 */
	public function getFoodAndVendorForFoodId($food_id){
		$query = $this->db->query('
			select 
				f.id food_id, f.name as food_name, f.alternate_name, f.price, f.descr, f.ingredients, 
				f.health_benefits, f.eating_instructions, 
				f.pickup_method, f.prep_time_hours prep_time,
				average_rating(f.id)/?*100 rating,
				total_orders(f.id) total_orders,
				u.id as user_id, u.name as user_name, u.email, u.phone, u.return_date,
				u.is_open
			from food f
			left join user u
			on u.id = f.user_id
			where
				f.id = ?
				and f.status = ?
				and u.status = ?',
			array(
				Food_review_model::$HIGHEST_RATING,
				$food_id,
				self::$ACTIVE_FOOD,
				User_model::$CERTIFIED_VENDOR
			));
		$results = $query->result();
		
		if (count($results) == 0)
			return false;
		else
			return $results[0];
	}
	
	
	/**
	 * Fetch pickup time information for the given food
	 * @return object with pickup method, pickup times, prep_time, return false if not found
	 */
	public function getFoodPickupTimesForFoodId($food_id){
		$query = $this->db->query('
			select 
				f.pickup_method, f.prep_time_hours,
				u.pickup_mon, u.pickup_tue, u.pickup_wed, u.pickup_thu, u.pickup_fri, 
				u.pickup_sat, u.pickup_sun
			from food f
			left join user u
			on u.id = f.user_id
			where
				f.id = ?
				and f.status = ?
				and u.status = ?',
			array(
				$food_id,
				self::$ACTIVE_FOOD,
				User_model::$CERTIFIED_VENDOR
			));
		$results = $query->result();
		
		if (count($results) == 0)
			return false;
		else
			return $results[0];
	}
	
	
	/**
	 * Return an array of food pictures
	 */
	public function getFoodPicturesForFoodId($food_id){
		$query = $this->db->query('
			select p.id, p.path
			from food_picture p
			where
				p.food_id = ?',
			array(
				$food_id
			));
		return $query->result();
	}
}