<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_model extends CI_Model {
	/**
	 * Save the stripe_id into a new payment
	 * Tag order_basket for payment
	 * This assumes that the provided order_basket is originally open
	 * @return True on success, error on failure
	 */
	public function payOpenBasket($amount, $basket_id, $stripe_id){
		$payment_id = 0;
		
		// add payment to database
		if (!$this->db->query('call add_payment(?, ?, @payment_id)', array($amount, $stripe_id))){
			return $this->db->error;
		} else {
			$query = $this->db->query('select @payment_id as payment_id');
			$payment_id = $query->result()[0]->payment_id;
		}
		
		// link payment to order_basket
		if (!$this->db->query('
			update order_basket
			set
				payment_id = ?
			where
				id = ?', array($payment_id, $basket_id))){
			
			return $this->db->error;
		}
		
		return true;
	}
	
	public function refundOrderItem($order_item_id, $stripe_id){
		
	}
}