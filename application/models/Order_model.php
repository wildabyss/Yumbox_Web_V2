<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_model extends CI_Model {
	/**
	 * Fetch the total number of orders placed on $food_id
	 */
	public function getTotalOrdersForFood($food_id){
		$query = $this->db->query('
			select sum(o.quantity) total
			from order_item o
			where
				o.food_id = ?', array($food_id));
				
		// return results
		$results = $query->result();
		if (count($results)==0)
			return 0;
		else
			return $results[0]->total;
	}
	
	
	/**
	 * Add a single order to the $order_basket_id
	 * @return true on success, error on failure
	 */
	public function addOrderToBasket($food_id, $order_basket_id){
		if (!$query = $this->db->query('call add_order(?,?,?)', 
			array($order_basket_id, $food_id, 1))){
			
			return $this->db->error();
		}
		
		return true;
	}
}