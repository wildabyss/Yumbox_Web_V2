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
	 *	is_rush		=> bool
	 *	vendor_id	 => int
	 *	min_rating	=> int
	 *	min_price	 => float
	 *	max_price	 => float
	 *  location	=> {latitude, longitude}
	 *	show_all	=> bool
	 */
	public function getAllActiveRelatedCategories(array $category_ids, $limit, array $filters){
		// sort through filters
		$is_rush = isset($filters["is_rush"])?$filters["is_rush"]:false;
		$vendor_id = isset($filters["vendor_id"])?$filters["vendor_id"]:false;
		$min_rating = isset($filters["min_rating"])?$filters["min_rating"]:false;
		$min_price = isset($filters["min_price"])?$filters["min_price"]:false;
		$max_price = isset($filters["max_price"])?$filters["max_price"]:false;
		$location = isset($filters["location"])?$filters["location"]:false;
		$show_all = isset($filters["show_all"])?$filters["show_all"]:false;
		
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
				left join address ad
				on ad.user_id = f.user_id
				where
					f.status = ? 
					and u.status > ?
					and u.is_open = 1';
					
		// filter out non-rush items		
		if ($is_rush){
			$query_str .= ' and f.pickup_method = ? and f.prep_time_hours <= ?';
		}
		// filter minimum rating
		if ($min_rating !== false){
			$query_str .= ' and average_rating(f.id) >= ?';
		}
		// filter prices
		if ($min_price !== false){
			$query_str .= ' and f.price >= ? and f.price <= ?';
		}
		// filter categories	
		$query_str .= ' and a.food_category_id in (';
		$counter = 0;
		foreach ($category_ids as $cat_id){
			if ($counter > 0)
				$query_str .= ",";
			$query_str .= $this->db->escape($cat_id);
			
			$counter++;
		}
		// filter locations
		if ($location !== false){
			$query_str .= ' and ad.longitude is not null and ad.latitude is not null
				and distance_btw_coords(ad.latitude, ad.longitude, ?, ?) <= ?';
		}
		// end search query
		$query_str .= ')) 
			order by 
				c.main desc, c.name asc';
		if (!$show_all){
			$query_str .= ' limit ?';
		}
		
		// add bindings
		$bindings = array(
			Food_model::$ACTIVE_FOOD,
			User_model::$INACTIVE_USER
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
	 * Fetch all active categories
	 *
	 * @param $filters:
	 *	is_rush		=> bool
	 *	vendor_id	 => int
	 *	min_rating	=> int
	 *	min_price	 => float
	 *	max_price	 => float
	 *  location	=> {latitude, longitude}
	 *	show_all	=> bool
	 */
	public function getAllActiveCategories($limit, array $filters){
		// sort through filters
		$is_rush = isset($filters["is_rush"])?$filters["is_rush"]:false;
		$vendor_id = isset($filters["vendor_id"])?$filters["vendor_id"]:false;
		$min_rating = isset($filters["min_rating"])?$filters["min_rating"]:false;
		$min_price = isset($filters["min_price"])?$filters["min_price"]:false;
		$max_price = isset($filters["max_price"])?$filters["max_price"]:false;
		$location = isset($filters["location"])?$filters["location"]:false;
		$show_all = isset($filters["show_all"])?$filters["show_all"]:false;
		
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
			left join address ad
			on ad.user_id = f.user_id
			where
				f.status = ?
				and u.status > ?
				and u.is_open = 1';
		
		// filter out non-rush items		
		if ($is_rush){
			$query_str .= ' and f.pickup_method = ? and f.prep_time_hours <= ?';
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
		// filter locations
		if ($location !== false){
			$query_str .= ' and ad.longitude is not null and ad.latitude is not null
				and distance_btw_coords(ad.latitude, ad.longitude, ?, ?) <= ?';
		}
		$query_str .= ' order by c.name';
		if (!$show_all){
			$query_str .= ' limit ?';
		}
	
		// bindings
		$bindings = array(
			Food_model::$ACTIVE_FOOD,
			User_model::$INACTIVE_USER
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
	
	
	/**
	 * Tag category_name with food
	 * @return true on success, error on failure
	 */
	public function addCategoryForFood($food_id, $category_name){
		$this->db->trans_start();
		
		// look for the category_name
		$query = $this->db->query('
			select c.id, c.name
			from food_category c
			where
				upper(c.name) = upper(?)', array(trim($category_name)));
		$results = $query->result();
		if (count($results)>0){
			// use this category for tagging
			
			$category_id = $results[0]->id;
			
			// check if it's already tagged
			$query = $this->db->query('
				select 1
				from food_category_assoc a
				where
					a.food_id=? and a.food_category_id=?', array($food_id, $category_id));
			$results = $query->result();
			if (count($results)==0){
				// tag it
				
				if (!$this->db->query('insert into food_category_assoc (food_id, food_category_id) values (?,?)', array($food_id, $category_id))){
					return $this->db->error;
				}
			} else {
				return "tag already exists";
			}
		} else {
			// create a new category
			
			if (!$this->db->query('insert into food_category (name) values (?)', array($category_name))){
				return $this->db->error;
			}
			$category_id = $this->db->insert_id();
			if (!$this->db->query('insert into food_category_assoc (food_id, food_category_id) values (?,?)', array($food_id, $category_id))){
				return $this->db->error;
			}
		}
		
		$this->db->trans_complete();
		return true;
	}
	
	
	/**
	 * Remove category_name tag from food
	 * @return true on success, error on failure
	 */
	public function removeCategoryForFood($food_id, $category_name){
		// look for the category_name
		$query = $this->db->query('
			select a.id
			from food_category_assoc a
			left join food_category c
			on c.id = a.food_category_id
			where
				upper(c.name) = upper(?)', array(trim($category_name)));
		$results = $query->result();
		if (count($results)>0){
			$assoc_id = $results[0]->id;
			
			if (!$this->db->query('delete from food_category_assoc where id=?', array($assoc_id))){
				return $this->db->error;
			}
		}
		
		return true;
	}
}
