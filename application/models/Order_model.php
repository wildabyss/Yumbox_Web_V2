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
	 * Change the quantity field in order
	 * @return true on success, error on failure
	 */
	public function changeOrderQuantity($order_id, $basket_id, $quantity){
		if (!$this->db->query('
			update order_item 
			set
				quantity = ?
			where
				id = ?
				and order_basket_id = ?', array($quantity, $order_id, $basket_id))){
			
			return $this->db->error();
		}
		
		return true;
	}
}