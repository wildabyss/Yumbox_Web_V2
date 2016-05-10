<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Food_review_model extends CI_Model {
	public static $HIGHEST_RATING = 5;
	
	public function getAllReviewsAndUsersForFood($food_id, $limit){
		$query = $this->db->query('
			select 
				r.id, round(r.rating/?*100) rating, r.review, r.user_id, u.name user_name
			from
				food_review r
			left join
				user u
			on 
				u.id = r.user_id
			where
				r.food_id = ?
			limit ?', 
			array(
				Food_review_model::$HIGHEST_RATING,
				$food_id, 
				$limit
		));
		return $query->result();
	}
}