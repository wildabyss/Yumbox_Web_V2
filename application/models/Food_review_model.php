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
			order by 
				created_on desc
			limit ?', 
			array(
				Food_review_model::$HIGHEST_RATING,
				$food_id, 
				$limit
		));
		return $query->result();
	}
	
	
	/**
	 * Fetch the review for id
	 * @return review object on success, false on failure
	 */
	public function getReviewForId($review_id){
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
				r.id = ?', 
			array(Food_review_model::$HIGHEST_RATING, $review_id));
		$results = $query->result();
		
		if (count($results)==0)
			return false;
		else
			return $results[0];
	}
	
	
	/**
	 * Determine whether the user can leave a review for the given food
	 * @return true or false
	 */
	public function canUserAddReviewForFood($user_id, $food_id){
		// get review quota
		$query = $this->db->query('
			select 
				count(o.id) avail
			from order_item o
			left join
				order_basket b
			on o.order_basket_id = b.id
			left join
				payment p
			on p.order_item_id = o.id
			where
				b.user_id = ?
				and o.food_id = ?
				and p.id is not null', array($user_id, $food_id));
		$results = $query->result();
		if (count($results)==0)
			return false;
		$avail = $results[0]->avail;
		
		// get reviews used up
		$query = $this->db->query('
			select
				count(r.id) used
			from food_review r
			where
				r.user_id = ?
				and r.food_id = ?', array($user_id, $food_id));
		$results = $query->result();
		$used = count($results)==0?0:$results[0]->used;
		
		return ($avail-$used)>0;
	}
	
	
	/**
	 * Add a new review
	 * @return review_id
	 * @throw Exception 
	 */
	public function addReviewForFood($user_id, $food_id, $rating, $review){
		$rating = min(max(1, $rating), self::$HIGHEST_RATING);
		
		if (!$this->db->query('insert into food_review
			(food_id, user_id, rating, review)
			values (?,?,?,?)', array($food_id, $user_id, $rating, $review))){
				
			throw new Exception($this->db->error());
		}
		
		return $this->db->insert_id();
	}
}