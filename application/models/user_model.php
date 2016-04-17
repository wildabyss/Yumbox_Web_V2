<?php

class User_model extends CI_Model {
	// status field
	public static $INACTIVE_USER = 0;
    public static $ACTIVE_USER = 1;
	public static $CERTIFIED_VENDOR = 2;
	
	// user type
	public static $CUSTOMER = 0;
    public static $VENDOR = 1;
	
	public function getUserForUserId($user_id){
		$query = $this->db->query('
			select 
				u.*
			from user u
			where
				u.id = ?',
			array(
				$user_id
			));
		$results = $query->result();
		
		if (count($results) == 0)
			return NULL;
		else
			return $results[0];
	}
	
	public function getUserForFacebookId($fb_id){
		$query = $this->db->query('
			select 
				u.*
			from user u
			where
				u.fb_id = ?',
			array(
				$fb_id
			));
		$results = $query->result();
		
		if (count($results) == 0)
			return NULL;
		else
			return $results[0];
	}
	
	public function addUser($user_type, $name, $email, $fb_id=NULL, $google_id=NULL){
		$fb_token = $fb_id==NULL?"null":"?";
		$google_token = $google_token==NULL?"null":"?";
		
		// bindings
		$bindings = array($user_type, $name, $email);
		if ($fb_id != NULL)
			$bindings[] = $fb_id;
		
		$query = $this->db->query("call add_user(?, ?, ?, $fb_token)", $bindings);
	}
}