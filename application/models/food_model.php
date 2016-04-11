<?php

class Food_model extends CI_Model {
	// food status
	public static $INACTIVE_FOOD = 0;
    public static $ACTIVE_FOOD = 1;
	
	// cutoff time grace period
	public static $CUTOFF_GRACE_MIN = 15;
	
	public function getActiveFoodsWithPicturesForCategory($categoryId, $limit){
		$query = $this->db->query('
			select f.*, p.path
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
				and u.status = ?
			group by f.id
			limit ?', 
			array(
				$categoryId, 
				Food_model::$ACTIVE_FOOD, 
				User_model::$CERTIFIED_VENDOR,
				$limit
			));
		return $query->result();
	}
	
	public function getQuickFoodsWithPicturesForCategory($categoryId, DateTime $orderDateTime, $limit){
		$query = $this->db->query('
			select f.*, p.path
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
				and (f.cutoff_time > addtime(?, ?) or f.cutoff_time = \'00:00:00\')
				and u.status = ?
				and (u.return_date is null or u.return_date < ?)
			group by f.id
			limit ?', 
			array(
				$categoryId, 
				Food_model::$ACTIVE_FOOD,
				$orderDateTime->format('H:i:s'),
				"00:".self::$CUTOFF_GRACE_MIN.":00}",
				User_model::$CERTIFIED_VENDOR,
				$orderDateTime->format(DateTime::ISO8601),
				$limit
			));
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