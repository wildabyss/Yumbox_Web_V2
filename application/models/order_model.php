<?php

class Order_model extends CI_Model {
	/**
	 * Fetch the total number of orders placed on $food_id
	 */
	public function GetTotalOrdersForFood($food_id){
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
}