<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Refund_model extends CI_Model {
	public static $TYPE_BUYER = 0;
	public static $TYPE_VENDOR = 1;
	
	/**
	 * Save the stripe_id into a new refund
	 * @return True on success, error on failure
	 */
	public function refundOrderItem($amount, $order_item_id, $stripe_id, $type, $explanation){
		// add payment to database
		if (!$this->db->query('call add_refund(?, ?, ?, ?, ?)', 
			array($amount, $stripe_id, $order_item_id, $type, $explanation))){
			return $this->db->error;
		}
		
		return true;
	}
}