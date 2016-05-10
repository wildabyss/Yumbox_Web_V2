<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {
	// status field
	public static $INACTIVE_USER = 0;
    public static $ACTIVE_USER = 1;
	public static $CERTIFIED_VENDOR = 2;
	
	// user type
	public static $CUSTOMER = 0;
    public static $VENDOR = 1;
	
	/**
	 * Fetch the user object
	 * Return false if user does not exist
	 */
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
			return false;
		else
			return $results[0];
	}
	
	/**
	 * Fetch the user object using the Facebook id
	 * Return false if user does not exist
	 */
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
			return false;
		else
			return $results[0];
	}
	
	/**
	 * Fetch the user object using the Google id
	 * Return false if user does not exist
	 */
	public function getUserForGoogleId($google_id){
		$query = $this->db->query('
			select 
				u.*
			from user u
			where
				u.google_id = ?',
			array(
				$google_id
			));
		$results = $query->result();
		
		if (count($results) == 0)
			return false;
		else
			return $results[0];
	}
	
	/**
	 * Add user to the database if it doesn't already exist
	 *
	 * @return TRUE on success, error code and message on failure
	 */
	public function addUser($user_type, $name, $email, $fb_id=NULL, $google_id=NULL){
		$fb_token = $fb_id==NULL?"null":"?";
		$google_token = $google_id==NULL?"null":"?";
		
		// bindings
		$bindings = array($user_type, $name, $email);
		if ($fb_id != NULL)
			$bindings[] = $fb_id;
		if ($google_id != NULL)
			$bindings[] = $google_id;
		
		if (!$this->db->query("call add_user(?, ?, ?, $fb_token, $google_token)", $bindings)){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Fetch the path to a single user picture
	 * Return false if non-existent
	 */
	public function getUserPicture($user_id){
		$query = $this->db->query('
			select p.path
			from user_picture p
			where p.user_id = ?
			limit 1', array($user_id)
		);
		
		$results = $query->result();
		
		if (count($results)==0)
			return false;
		else
			return $results[0]->path;
	}
}