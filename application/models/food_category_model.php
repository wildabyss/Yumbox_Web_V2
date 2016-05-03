<?php

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
	 */
	public function getAllActiveRelatedCategories(array $category_ids, DateTime $orderDateTime=NULL){
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
					
		if ($orderDateTime != NULL){
			// filter cut-off times
			$query_str .= ' and (f.cutoff_time > addtime(?, ?) or f.cutoff_time = \'00:00:00\')
				and (u.return_date is null or u.return_date < ?)';
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
		if ($orderDateTime != NULL){
			$bindings[] = $orderDateTime->format('H:i:s');
			$bindings[] = "00:".Food_model::$CUTOFF_GRACE_MIN.":00";
			$bindings[] = $orderDateTime->format(DateTime::ISO8601);
		}
		
		// perform database query
		$query = $this->db->query($query_str, $bindings);
		return $query->result();
	}
	
	/**
	 * Fetch all active categories
	 * @param DateTime $orderDateTime filter with datetime cutoff
	 * @param $user_id filter with foods belonging to user_id
	 */
    public function getAllActiveCategories(DateTime $orderDateTime=NULL, $user_id=NULL){
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
		
		// cut-off time filter
		if ($orderDateTime != NULL){
			$query_str .= ' and (f.cutoff_time > addtime(?, ?) or f.cutoff_time = \'00:00:00\')
				and (u.return_date is null or u.return_date < ?)';
		}
		
		// user filter
		if ($user_id != NULL){
			$query_str .= ' and u.id = ?';
		}
	
		// bindings
		$bindings = array(
			Food_model::$ACTIVE_FOOD,
			User_model::$CERTIFIED_VENDOR
		);
		if ($orderDateTime != NULL){
			$bindings[] = $orderDateTime->format('H:i:s');
			$bindings[] = "00:".Food_model::$CUTOFF_GRACE_MIN.":00";
			$bindings[] = $orderDateTime->format(DateTime::ISO8601);
		}
		if ($user_id != NULL){
			$bindings[] = $user_id;
		}
		
		// perform database query
		$query = $this->db->query($query_str, $bindings);
		return $query->result();
	}
}