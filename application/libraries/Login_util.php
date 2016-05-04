<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_util {
	/**
	 * Return true if the user has logged in
	 */
	public function isUserLoggedIn()
	{
		if (isset($_SESSION['user_id']) 
			&& (isset($_SESSION['fb_token']) || isset($_SESSION['google_token']))){
			// session exists
			return true;
		} else{
			return false;
		}
	}
	
	/**
	 * Return user_id if it exists
	 * False otherwise
	 */
	public function getUserId(){
		if ($this->isUserLoggedIn())
			return $_SESSION['user_id'];
		else
			return false;
	}
}