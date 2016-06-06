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
	 *	is_rush		=> bool
	 *	is_open		=> bool
	 *	category_ids   => array
	 *	food_ids		=> array
	 *	vendor_id	 => int
	 *	min_rating	=> int
	 *	min_price	 => float
	 *	max_price	 => float
	 * 	location	=> [latitude, longitude]
	 *	show_all	=> bool
	 */
	public function getActiveFoodsAndVendorAndOrdersAndRatingAndPictures($limit, array $filters){
		
		// sort through filters
		$is_rush = isset($filters["is_rush"])?$filters["is_rush"]:false;
		$is_open = isset($filters["is_open"])?$filters["is_open"]:true;
		$category_ids = isset($filters["category_ids"])?$filters["category_ids"]:false;
		$food_ids = isset($filters["food_ids"])?$filters["food_ids"]:false;
		$vendor_id = isset($filters["vendor_id"])?$filters["vendor_id"]:false;
		$min_rating = isset($filters["min_rating"])?$filters["min_rating"]:false;
		$min_price = isset($filters["min_price"])?$filters["min_price"]:false;
		$max_price = isset($filters["max_price"])?$filters["max_price"]:false;
		$location = isset($filters["location"])?$filters["location"]:false;
		$show_all = isset($filters["show_all"])?$filters["show_all"]:false;
	
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
			from food f
			left join food_category_assoc a
			on f.id = a.food_id
			left join user u
			on u.id = f.user_id
			left join address ad
			on ad.user_id = f.user_id
			left join food_picture p
			on p.food_id = f.id
			where
				f.status = ?
				and u.status > ?
				and u.is_open >= ?';
		
		// filter out non-rush items
		if ($is_rush){
			$query_str .= ' and f.pickup_method = ? and f.prep_time_hours <= ?';
		} 
		// filter selected categories
		if ($category_ids !== false){
			$query_str .= ' and a.food_category_id in (';
			for ($i=0; $i<count($category_ids); $i++){
				if ($i>0) $query_str .= ",";
				$query_str .= $this->db->escape($category_ids[$i]);
			}
			$query_str .= ")";
		}

		// filter food ids from fulltext search
		if ($food_ids !== false){
			$query_str .= ' and f.id in (';
			for ($i=0; $i<count($food_ids); $i++){
				if ($i>0) $query_str .= ",";
				$query_str .= $this->db->escape($food_ids[$i]);
			}
			$query_str .= ")";
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
		// filter locations
		if ($location !== false){
			$query_str .= ' and ad.longitude is not null and ad.latitude is not null
				and distance_btw_coords(ad.latitude, ad.longitude, ?, ?) <= ?';
		}
		$query_str .= ' group by f.id order by f.name';
		if (!$show_all){
			$query_str .= ' limit ?';
		}
		
		// bindings
		$bindings = array(
			Food_review_model::$HIGHEST_RATING,
			Food_model::$ACTIVE_FOOD, 
			User_model::$INACTIVE_USER,
			$is_open,
		);
		if ($is_rush){
			$bindings[] = Food_model::$PICKUP_ANYTIME;
			$bindings[] = Time_prediction::$RUSH_HOUR_CUTOFF;
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
		if ($location !== false){
			$bindings[] = $location["latitude"];
			$bindings[] = $location["longitude"];
			$bindings[] = Search::$SEARCH_RADIUS;
		}
		if (!$show_all){
			$bindings[] = $limit;
		}
		
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
				f.id food_id, f.status food_status, f.name food_name, f.alternate_name food_alt_name, f.price food_price, f.descr, f.ingredients, 
				f.health_benefits, f.eating_instructions, 
				f.pickup_method, f.prep_time_hours prep_time, f.quota,
				p.path pic_path,
				average_rating(f.id)/?*100 rating,
				total_orders(f.id) total_orders,
				u.id vendor_id, u.name vendor_name, u.email, u.phone,
				u.is_open, u.status vendor_status
			from food f
			left join user u
			on u.id = f.user_id
			left join food_picture p
			on p.food_id = f.id
			where
				f.id = ?
			group by f.id',
			array(
				Food_review_model::$HIGHEST_RATING,
				$food_id
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
				f.id = ?',
			array($food_id));
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
	
	
	/**
	 * Create new food
	 * @return food_id
	 * @throw Exception on error
	 */
	public function createFood($user_id, $food_name, $food_alt_name, $price){
		if (!$this->db->query('
			insert into food
				(user_id, name, price, alternate_name)
			values
				(?,?,?,?)', array(
				$user_id,
				trim($food_name),
				$price,
				trim($food_alt_name)
		))){
			throw new Exception($this->db->error());
		}
		
		return $this->db->insert_id();
	}
	
	
	/**
	 * Remove the specified food
	 * Mark its status as inactive
	 * @return true on success, error on failure
	 */
	public function removeFood($food_id){
		if (!$this->db->query('update food set status = 0 where id=?', array($food_id))){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Change the name of the food
	 * @return true on success, error on failure
	 */
	public function changeName($food_id, $name){
		if (!$this->db->query('update food set name = ? where id=?', array($name, $food_id))){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Change the name of the food
	 * @return true on success, error on failure
	 */
	public function changeAlternateName($food_id, $alt_name){
		if (!$this->db->query('update food set alternate_name = ? where id=?', array($alt_name, $food_id))){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Change the price of the food
	 * @return true on success, error on failure
	 */
	public function changePrice($food_id, $price){
		if (!$this->db->query('update food set price = ? where id=?', array($price, $food_id))){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Change the description
	 * @return true on success, error on failure
	 */
	public function changeDescription($food_id, $descr){
		if (!$this->db->query('update food set descr = ? where id=?', array($descr, $food_id))){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Change the description
	 * @return true on success, error on failure
	 */
	public function changeIngredients($food_id, $ingredients){
		if (!$this->db->query('update food set ingredients = ? where id=?', array($ingredients, $food_id))){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Change the description
	 * @return true on success, error on failure
	 */
	public function changeHealthBenefits($food_id, $benefits){
		if (!$this->db->query('update food set health_benefits = ? where id=?', array($benefits, $food_id))){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Change the description
	 * @return true on success, error on failure
	 */
	public function changeEatingInstructions($food_id, $instructions){
		if (!$this->db->query('update food set eating_instructions = ? where id=?', array($instructions, $food_id))){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Modify the display picture of the user
	 * @return true on sucess, error on failure
	 */
	public function modifyFoodPicture($food_id, $pic_path){
		// associate with new file
		if (!$this->db->query("call add_food_picture(?, ?)", array($food_id, $pic_path))){
			return $this->db->error();
		}

		return true;
	}
	
	
	public function modifyPickupMethod($food_id, $method){
		if ($method != self::$PICKUP_ANYTIME && $method != self::$PICKUP_DESIGNATED){
			return "incorrect pickup method";
		}
		
		if (!$this->db->query("update food set pickup_method=? where id=?", array($method, $food_id))){
			return $this->db->error();
		}

		return true;
	}
	
	
	public function modifyPreparationTime($food_id, $time){
		if (!is_numeric($time) || $time<=0){
			return "incorrect time";
		}
		
		if (!$this->db->query("update food set prep_time_hours=? where id=?", array($time, $food_id))){
			return $this->db->error();
		}

		return true;
	}
}
