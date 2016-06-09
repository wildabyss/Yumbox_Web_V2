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
				f.id food_id, f.name food_name, f.alternate_name food_alt_name, f.price, f.prep_time_hours prep_time,
				f.user_id vendor_id, b.user_id buyer_id, b.order_date, b.id order_basket_id,
				f.quota,
				p.id payment_id, p.tax_rate, p.take_rate,
				o.id order_id, o.quantity, o.is_filled, r.id refund_id,
				po.id payout_id, po.take_rate vendor_take_rate,
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
			left join
				payout po
			on po.order_item_id = o.id
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
				o.food_id = ?
			group by o.food_id', array($food_id));
				
		// return results
		$results = $query->result();
		if (count($results)==0)
			return 0;
		else
			return $results[0]->total;
	}
	
	
	/**
	 * Fetch the total number of orders placed on $food_id that're unfilled
	 * They will count toward the quota
	 */
	public function getTotalUnfilledOrdersForFood($food_id){
		$query = $this->db->query('
			select sum(o.quantity) total
			from order_item o
			left join
				food f
			on f.id = o.food_id
			left join
				refund r
			on r.order_item_id = o.id
			where
				f.id = ?
				and o.is_filled = 0
				and r.id is null
			group by f.id', array($food_id));
		
		// return results
		$results = $query->result();
		if (count($results)==0)
			return 0;
		else
			return $results[0]->total;
	}
	
	
	/**
	 * Fetch all paid orders for vendor
	 * @param bool $is_filled 
	 * 		true: fetch only filled or canceled orders
	 *		false: fetch only unfilled orders
	 */
	public function getPaidOrdersForVendor($vendor_id, $is_filled){
		$query_str = 'select 
				f.id food_id, f.name food_name, f.alternate_name food_alt_name, f.price, f.prep_time_hours prep_time,
				b.id buyer_id, b.name buyer_name, basket.order_date,
				po.amount payout_amount, po.take_rate vendor_take_rate,
				p.amount payment_amount, p.tax_rate, p.take_rate take_rate,
				o.id order_id, o.quantity, o.is_filled, r.id refund_id,
				fp.path
			from order_item o
			left join
				food f
			on f.id = o.food_id
			left join
				food_picture fp
			on fp.food_id = f.id
			left join
				order_basket basket
			on o.order_basket_id = basket.id
			left join
				user b
			on b.id = basket.user_id
			left join
				payout po
			on po.order_item_id = o.id
			left join
				payment p
			on p.order_item_id = o.id
			left join
				refund r
			on r.order_item_id = o.id
			where
				f.user_id = ?';
				
		if ($is_filled){
			$query_str .= ' and (o.is_filled = 1 or r.id is not null)';
		} else {
			$query_str .= ' and o.is_filled = 0 and r.id is null and po.id is not null';
		}
		$query_str .= ' group by o.id';
		
		$query = $this->db->query($query_str, array($vendor_id));
		return $query->result();
	}
	
	
	/**
	 * Change the quantity field in order
	 * @return true on success, error on failure
	 */
	public function changeOrderQuantity($order_id, $basket_id, $quantity){
		// get current order_item
		$current_order = $this->getFoodOrder($order_id);
		
		// get current quantity and quota
		$query = $this->db->query('
			select sum(o.quantity) total
			from order_item o
			left join
				refund r
			on r.order_item_id = o.id
			where
				o.food_id = ?
				and o.id <> ?
				and o.is_filled = 0
				and r.id is null
			group by o.food_id', array($current_order->food_id, $order_id));
		$results = $query->result();
		$other_orders = 0;
		if (count($results)==0)
			$other_orders = 0;
		else
			$other_orders = $results[0]->total;
		
		// assess whether we can make the quantity change
		if ($other_orders + $quantity > $current_order->quota){
			return "chef cannot take on more orders";
		}
		
		// make the db change
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
	
	
	/**
	 * Set the status of the order to finished
	 * @return true on success, error on failure
	 */
	public function finishOrder($order_id){
		if (!$this->db->query('
			update order_item 
			set
				is_filled = 1
			where
				id = ?', array($order_id))){
			
			return $this->db->error();
		}
		
		return true;
	}
}