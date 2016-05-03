<?php

class Food_model extends CI_Model {
	// food status
	public static $INACTIVE_FOOD = 0;
    public static $ACTIVE_FOOD = 1;
	
	// cutoff time grace period
	public static $CUTOFF_GRACE_MIN = 15;
	
	public function getActiveFoodsAndVendorWithPicturesForCategory($categoryId, $limit, DateTime $orderDateTime=NULL, $user_id=NULL){
		// base query
		$query_str = '
			select 
				f.id food_id, f.name food_name, f.alternate_name food_alt_name, f.price food_price,
				f.cutoff_time cutoff_time,
				p.path pic_path,
				u.id vendor_id, u.name vendor_name
			from food_category_assoc a
			left join food f
			on f.id = a.food_id
			left join user u
			on u.id = f.user_id
			left join food_picture p
			on p.food_id = f.id
			where
				a.food_category_id = ?
				and f.status = ?
				and u.status = ?';
				
		// filter cut-off time
		if ($orderDateTime != NULL){
			$query_str .= ' and (f.cutoff_time > addtime(?, ?) or f.cutoff_time = \'00:00:00\')
				and (u.return_date is null or u.return_date < ?)';
		}
		
		// filter user
		if ($user_id != NULL){
			$query_str .= ' and u.id = ?';
		}
		$query_str .= ' group by f.id limit ?';
		
		// bindings
		$bindings = array(
			$categoryId, 
			Food_model::$ACTIVE_FOOD, 
			User_model::$CERTIFIED_VENDOR
		);
		if ($orderDateTime != NULL){
			$bindings[] = $orderDateTime->format('H:i:s');
			$bindings[] = "00:".self::$CUTOFF_GRACE_MIN.":00";
			$bindings[] = $orderDateTime->format(DateTime::ISO8601);
		}
		if ($user_id != NULL){
			$bindings[] = $user_id;
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
				f.name as food_name, f.price, f.cutoff_time, f.descr, f.ingredients, f.health_benefits,
				u.id as user_id, u.name as user_name, u.email, u.phone, u.return_date
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