<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Food_category_model extends CI_Model {
	/**
	 * Fetch all categories whose 'main' field is 1
	 */
	public function getAllMainCategories(){
		$query = $this->db->query('
			select c.id, c.name
			from food_category c
			where
				main = 1
			order by
				name asc');
		return $query->result();
	}
	
	/*
	 * Fetch all categories that have associations with the provided categories
	 *
	 * @param $filters:
	 *    is_rush		=> bool
	 *    can_deliver   => bool
	 *    vendor_id     => int
	 *    min_rating	=> int
	 *    min_price     => float
	 *    max_price     => float
	 *    max_time      => float
	 */
	public function getAllActiveRelatedCategories(array $category_ids, array $filters){
		// sort through filters
		$is_rush = isset($filters["is_rush"])?$filters["is_rush"]:false;
		$can_deliver = isset($filters["can_deliver"])?$filters["can_deliver"]:false;
		$vendor_id = isset($filters["vendor_id"])?$filters["vendor_id"]:false;
		$min_rating = isset($filters["min_rating"])?$filters["min_rating"]:false;
		$min_price = isset($filters["min_price"])?$filters["min_price"]:false;
		$max_price = isset($filters["max_price"])?$filters["max_price"]:false;
		$max_time = isset($filters["max_time"])?$filters["max_time"]:false;
		
		// base query
		$query_str = '
			select distinct c.id, c.name
			from food_category_assoc a
			left join food_category c
			on a.food_category_id = c.id
			where a.food_id in
				(select f.id
				from food f
				left join food_category_assoc a
				on a.food_id = f.id
				left join user u
				on u.id = f.user_id
				where
					f.status = ? 
					and u.status = ?';
					
		// filter out non-rush items		
		if ($is_rush){
			$query_str .= ' and u.is_open = 1';
		} 
		// filter max prep time
		if ($max_time !== false){
			$query_str .= ' and f.prep_time_hours <= ?';
		}
		// filter minimum rating
		if ($min_rating !== false){
			$query_str .= ' and average_rating(f.id) >= ?';
		}
		// filter prices
		if ($min_price !== false){
			$query_str .= ' and f.price >= ? and f.price <= ?';
		}
					
		$query_str .= ' and a.food_category_id in (';
		$counter = 0;
		foreach ($category_ids as $cat_id){
			if ($counter > 0)
				$query_str .= ",";
			$query_str .= $this->db->escape($cat_id);
			
			$counter++;
		}
		$query_str .= ')) order by c.name asc';
		
		// add bindings
		$bindings = array(
			Food_model::$ACTIVE_FOOD,
			User_model::$CERTIFIED_VENDOR
		);
		if ($max_time !== false){
			$bindings[] = Food_model::getMaxHoursForMaxTimeFilter($max_time);
		}
		if ($min_rating !== false){
			$bindings[] = $min_rating;
		}
		if ($min_price !== false){
			$bindings[] = $min_price;
			$bindings[] = $max_price;
		}
		
		// perform database query
		$query = $this->db->query($query_str, $bindings);
		return $query->result();
	}
	
	/**
	 * Fetch all active categories
	 *
	 * @param $filters:
	 *    is_rush		=> bool
	 *    can_deliver   => bool
	 *    vendor_id     => int
	 *    min_rating	=> int
	 *    min_price     => float
	 *    max_price     => float
	 *    max_time      => float
	 */
    public function getAllActiveCategories(array $filters){
		// sort through filters
		$is_rush = isset($filters["is_rush"])?$filters["is_rush"]:false;
		$can_deliver = isset($filters["can_deliver"])?$filters["can_deliver"]:false;
		$vendor_id = isset($filters["vendor_id"])?$filters["vendor_id"]:false;
		$min_rating = isset($filters["min_rating"])?$filters["min_rating"]:false;
		$min_price = isset($filters["min_price"])?$filters["min_price"]:false;
		$max_price = isset($filters["max_price"])?$filters["max_price"]:false;
		$max_time = isset($filters["max_time"])?$filters["max_time"]:false;
		
		// base query string
		$query_str = '
			select distinct c.id, c.name 
			from food_category_assoc a
			left join food_category c
			on c.id = a.food_category_id
			left join food f
			on f.id = a.food_id
			left join user u
			on u.id = f.user_id
			where
				f.status = ?
				and u.status = ?';
		
		// filter out non-rush items		
		if ($is_rush){
			$query_str .= ' and u.is_open = 1';
		} 
		// filter max prep time
		if ($max_time !== false){
			$query_str .= ' and f.prep_time_hours <= ?';
		}
		// filter minimum rating
		if ($min_rating !== false){
			$query_str .= ' and average_rating(f.id) >= ?';
		}
		// filter prices
		if ($min_price !== false){
			$query_str .= ' and f.price >= ? and f.price <= ?';
		}
		// user filter
		if ($vendor_id !== false){
			$query_str .= ' and u.id = ?';
		}
	
		// bindings
		$bindings = array(
			Food_model::$ACTIVE_FOOD,
			User_model::$CERTIFIED_VENDOR
		);
		if ($max_time !== false){
			$bindings[] = Food_model::getMaxHoursForMaxTimeFilter($max_time);
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
		
		// perform database query
		$query = $this->db->query($query_str, $bindings);
		return $query->result();
	}
	
	
	/**
	 * Fetch all food_categories for the given $food_id
	 */
	public function getAllCategoriesForFood($food_id){
		$query = $this->db->query('
			select c.id, c.name
			from
				food_category c
			left join
				food_category_assoc a
			on
				a.food_category_id = c.id
			where
				a.food_id = ?', array($food_id));
		return $query->result();
	}
}