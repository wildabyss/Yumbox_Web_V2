<?php

class User_follow_model extends CI_Model {
	/**
	 * Fetch the total number of active users who follow $user_id
	 */
	public function getNumberOfActiveFollowersForUser($vendor_id){
		$query = $this->db->query('
			select count(1) num
			from
				user_follow_assoc a
			left join 
				user u
			on
				u.id = a.user_id
			where
				u.status >= ?
				and a.vendor_id = ?
			group by
				user_id', 
			array(
				User_model::$ACTIVE_USER,
				$vendor_id
			));
		
		// return results
		$results = $query->result();
		if (count($results)==0)
			return 0;
		else
			return $results[0]->num;
	}
	
	/**
	 * Make $user_id follow $vendor_id
	 */
	public function addFollower($user_id, $vendor_id){
		// bindings
		$bindings = array($user_id, $vendor_id);
		
		if (!$this->db->query("call add_user(?, ?)", $bindings)){
			return $this->db->error();
		}
		
		return true;
	}
}