<?php

class Food_category_model extends CI_Model {

    public function getAllActiveCategories(){
		$query = $this->db->query('
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
				and u.status = ?', 
			array(
				Food_model::$ACTIVE_FOOD,
				User_model::$CERTIFIED_VENDOR
			));
		return $query->result();
	}
	
	public function getAllQuickFoodCategories(DateTime $orderDateTime){
		$query = $this->db->query('
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
				and (f.cutoff_time > addtime(?, ?) or f.cutoff_time = \'00:00:00\')
				and u.status = ?
				and (u.return_date is null or u.return_date < ?)', 
			array(
				Food_model::$ACTIVE_FOOD,
				$orderDateTime->format('H:i:s'),
				"00:".Food_model::$CUTOFF_GRACE_MIN.":00}",
				User_model::$CERTIFIED_VENDOR,
				$orderDateTime->format(DateTime::ISO8601)
			));
		return $query->result();
	}
}