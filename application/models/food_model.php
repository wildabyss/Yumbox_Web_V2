<?php

class Food_model extends CI_Model {
	// food status
	public static $INACTIVE_FOOD = 0;
    public static $ACTIVE_FOOD = 1;
	
	// max time in hours to be considered a rush item
	public static $MAX_RUSH_HOURS = 1.0;
	
	/**
	 * For a given $categoryId, fetch all the foods with its pictures up to $limit
	 * Fetch also its total number of orders and its aggregate rating
	 *
	 * @param $filters:
	 *    is_rush		=> bool
	 *    vendor_id     => int
	 */
	public function getActiveFoodsAndVendorAndOrdersAndRatingWithPicturesForCategory(
		$categoryId, $limit, array $filters){
		
		// sort through filters
		$is_rush = isset($filters["is_rush"])?$filters["is_rush"]:false;
		$vendor_id = isset($filters["vendor_id"])?$filters["vendor_id"]:NULL;
		
		// base query
		$query_str = '
			select 
				f.id food_id, f.name food_name, f.alternate_name food_alt_name, f.price food_price,
				u.is_open,
				p.path pic_path,
				orders.total total_orders,
				review.rating,
				u.id vendor_id, u.name vendor_name
			from food_category_assoc a
			left join food f
			on f.id = a.food_id
			left join user u
			on u.id = f.user_id
			left join food_picture p
			on p.food_id = f.id
			left join
				(select o.food_id, sum(o.quantity) total
				from order_item o
				group by o.food_id) orders
			on
				orders.food_id = f.id
			left join
				(select r.food_id, round(avg(r.rating)/?*100) rating
				from food_review r
				group by r.food_id) review
			on
				review.food_id = f.id
			where
				a.food_category_id = ?
				and f.status = ?
				and u.status = ?';
				
		// filter out non-rush
		if ($is_rush){
			$query_str .= ' and f.prep_time_hours <= ?
				and u.is_open = 1';
		}
		
		// filter user
		if ($vendor_id != NULL){
			$query_str .= ' and u.id = ?';
		}
		$query_str .= ' group by f.id limit ?';
		
		// bindings
		$bindings = array(
			Food_review_model::$HIGHEST_RATING,
			$categoryId, 
			Food_model::$ACTIVE_FOOD, 
			User_model::$CERTIFIED_VENDOR
		);
		if ($is_rush){
			$bindings[] = self::$MAX_RUSH_HOURS;
		}
		if ($vendor_id != NULL){
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
	 * @return: an object with food and vendor info, or NULL if unavailable
	 */
	public function getFoodAndVendorForFoodId($food_id){
		$query = $this->db->query('
			select 
				f.name as food_name, f.price, f.descr, f.ingredients, f.health_benefits,
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
				$food_id,
				self::$ACTIVE_FOOD,
				User_model::$CERTIFIED_VENDOR
			));
		$results = $query->result();
		
		if (count($results) == 0)
			return NULL;
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