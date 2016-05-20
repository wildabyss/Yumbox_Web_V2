<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_model extends CI_Model {
	/**
	 * Fetch the payment object for id
	 * @return false on failure
	 */
	public function getPayment($payment_id){
		$query = $this->db->query('
			select
				id, amount, tax_rate, take_rate, payment_date, order_item_id, stripe_charge_id
			from
				payment
			where
				id = ?', array($payment_id));
		
		$results = $query->result();
		if (count($results)==0)
			return false;
		else
			return $results[0];
	}
	
	
	/**
	 * Save the stripe_id into a new payment
	 * Tag order_basket for payment
	 * This assumes that the provided order_basket is originally open
	 * @return True on success, error on failure
	 */
	public function payOrderItem($amount, $take_rate, $tax_rate, $order_item_id, $stripe_id){	
		// add payment to database
		if (!$this->db->query('call add_payment(?, ?, ?, ?, ?)', 
			array(
				$amount, 
				$take_rate,
				$tax_rate,
				$stripe_id, 
				$order_item_id)
			)){
			return $this->db->error;
		}
		
		return true;
	}
}