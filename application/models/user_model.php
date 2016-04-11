<?php

class User_model extends CI_Model {
	// status field
	public static $INACTIVE_USER = 0;
    public static $ACTIVE_USER = 1;
	public static $CERTIFIED_VENDOR = 2;
	
	// user type
	public static $CUSTOMER = 0;
    public static $VENDOR = 1;
}