<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_model extends CI_Model {
	public static $IS_FILLED_UNFILLED = 0;
	public static $IS_FILLED_DELIVERED = 1;
	
	
	/**
	 * Fetch order_item for order_id
	 * @return false if no order item found
	 */
	public function getFoodOrder($order_id){
		$query = $this->db->query('
			select
				f.id food_id, f.name, f.alternate_name, f.price, f.prep_time_hours,
				f.user_id vendor_id, b.user_id buyer_id, p.id payment_id, b.order_date, b.id order_basket_id,
				o.id order_id, o.quantity, o.is_filled, r.id refund_id,
				fp.path
			from
				order_item o
			left join
				food f
			on f.id = o.food_id
			left join
				food_picture fp
			on fp.food_id = f.id
			left join
				order_basket b
			on b.id = o.order_basket_id
			left join
				payment p
			on p.order_item_id = o.id
			left join
				refund r
			on r.order_item_id = o.id
			where
				o.id = ?
			group by f.id', array($order_id));
		
		$results = $query->result();
		
		if (count($results)==0)
			return false;
		else
			return $results[0];
	}
	
	
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