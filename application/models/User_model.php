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
				u.id, u.user_type, u.status, u.name, u.email, u.descr,
				u.is_open, 
				u.pickup_mon, u.pickup_tue, u.pickup_wed, u.pickup_thu, u.pickup_fri,
				u.pickup_sat, u.pickup_sun,
				u.fb_id, u.google_id,
				a.address, a.city, a.province, a.postal_code, a.country, a.latitude, a.longitude
			from user u
			left join
				address a
			on
				a.user_id = u.id
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
				u.id, u.user_type, u.status, u.name, u.email,
				u.is_open, u.fb_id, u.google_id
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
				u.id, u.user_type, u.status, u.name, u.email,
				u.is_open, u.fb_id, u.google_id
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
	
	
	/**
	 * Attempt to fetch the address for $user_id
	 * Create one if it doesn't exist
	 * Assume here that user exists
	 */
	public function getOrCreateAddress($user_id){
		$address = false;
		
		do {
			// fetch address for user
			$query = $this->db->query('
				select
					a.address, a.city, a.province, a.postal_code, a.country,
					a.latitude, a.longitude
				from
					address a
				where
					a.user_id = ?', array("$user_id"));
			$results = $query->result();
			if (count($results)>=1){
				$address = $results[0];
			} else {
				// doesn't exist, create one
				
				if (!$query = $this->db->query('
					insert into address
						(user_id)
					values
						(?)', array($user_id))){
					
					throw new Exception($this->db->error);
				}
			}

		} while ($address === false);
		
		return $address;
	}
	
	
	/**
	 * Fetch the address information of the user and concatenate to string
	 * @return false if user not exist
	 */
	public function getUserAddressString($user_id){
		$query = $this->db->query('
			select 
				trim(concat_ws(\' \', a.address, a.city, a.province, a.postal_code, a.country)) address
			from user u
			left join
				address a
			on
				a.user_id = u.id
			where
				u.id = ?',
			array(
				$user_id
			));
		$results = $query->result();
		
		if (count($results) == 0)
			return false;
		else
			return $results[0]->address;
	}
	
	
	/**
	 * Return true if user has active foods
	 */
	public function isUserAChef($user_id){
		$query = $this->db->query('
			select 1
			from user u
			left join
				food f
			on f.user_id = u.id
			where
				f.status = ?
				and u.id = ?
			limit 1', array(Food_model::$ACTIVE_FOOD, $user_id));
		$results = $query->result();
		
		if (count($results)==0)
			return false;
		else
			return true;
	}
	
	
	/**
	 * Modify user name
	 * Return true on success, error on failure
	 */
	public function modifyUsername($user_id, $username){
		if (!$this->db->query('update user set name = ? where id = ?', [trim($username), $user_id])){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Modify user description
	 * Return true on success, error on failure
	 */
	public function modifyUserDescription($user_id, $descr){
		if (!$this->db->query('update user set descr = ? where id = ?', [trim($descr), $user_id])){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Modify user email
	 * Return true on success, error on failure
	 */
	public function modifyEmail($user_id, $email){
		if (!$this->db->query('update user set email = ? where id = ?', [trim($email), $user_id])){
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Modify address for the given user
	 */
	public function modifyAddress($user_id, $address, $city, $province, $country, $postal_code){
		$this->db->trans_start();
		
		// save the plain-text address information
		if (!$this->db->query('
			update address set address=?, city=?, province=?, country=?, postal_code=?
			where user_id = ?', array(
				trim($address),
				trim($city),
				trim($province),
				trim($country),
				trim($postal_code),
				trim($user_id)
			))){
			
			return $this->db->error();
		}
		
		// geocode
		$coords = $this->search->geocodeLocation($address.' '.$city.' '.$province.' '.$country.' '.$postal_code);
		if ($coords === false){
			return "cannot geocode";
		}
		
		// save coordinates
		if (!$this->db->query('
			update address set latitude=?, longitude=?
			where user_id = ?', array(
				$coords["latitude"],
				$coords["longitude"],
				trim($user_id)
			))){
			
			return $this->db->error();
		}
		
		$this->db->trans_complete();
		
		return true;
	}
	
	
	/**
	 * Modify the display picture of the user
	 * @return true on sucess, error on failure
	 */
	public function modifyUserPicture($user_id, $pic_path){
		// associate with new file
		if (!$this->db->query("call add_user_picture(?, ?)", array($user_id, $pic_path))){
			return $this->db->error();
		}

		return true;
	}
	
}
