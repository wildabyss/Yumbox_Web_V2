<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_basket_model extends CI_Model {
	/**
	 * Fetch the order_basket for a given id and user_id
	 * Having the user_id ensures that the basket being retrieved belongs to the user
	 * @return false if not exist
	 */
	public function getOrderBasketForUser($basket_id, $user_id){
		$query = $this->db->query('
			select
				b.order_date, b.delivery_address, b.is_filled, b.payment_id
			from
				order_basket b
			where
				b.id = ?
				and b.user_id = ?', 
			array($basket_id, $user_id));
		
		$results = $query->result();
		if (count($results)==0)
			return false;
		else
			return $results[0];
	}
	
	
	/**
	 * Fetch the order_basket for a given $user_id that hasn't finished checkout
	 * i.e. payment is null
	 * @return false if not exist
	 */
	public function getOpenBasketForUser($user_id){
		$query = $this->db->query('
			select
				b.id, b.order_date, b.delivery_address
			from
				order_basket b
			where
				b.payment_id is null
				and b.user_id = ?', array($user_id));
				
		// return results
		$results = $query->result();
		if (count($results)==0)
			return false;
		else
			return $results[0];
	}
	
	
	/**
	 * Create an open order_basket for $user_id
	 * @return true on success, error on failure
	 */
	public function createOpenBasketForUser($user_id){
		if (!$query = $this->db->query('
			insert into order_basket
				(order_date, user_id)
			values
				(now(), ?)', array($user_id))){
			
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Attempt to fetch the open basket for $user_id
	 * Create one if it doesn't exist
	 */
	public function getOrCreateOpenBasket($user_id){
		do {
			$order_basket = $this->getOpenBasketForUser($user_id);
			if ($order_basket === false){
				// create one if it doesn't exist
				$res = $this->createOpenBasketForUser($user_id);
				if ($res !== true){
					throw new Exception($error);
					return;
				}
			}
		} while ($order_basket === false);
		
		return $order_basket;
	}
	
	
	/**
	 * Fetch all vendors in the order basket
	 */
	public function getAllVendorsInBasket($basket_id){
		$query = $this->db->query('
			select
				u.id, u.name, u.is_open
			from
				order_item o
			left join food f
			on f.id = o.food_id
			left join user u
			on u.id = f.user_id
			where
				o.order_basket_id = ?
			group by
				u.id', array($basket_id));
		
		return $query->result();
	}
	
	
	/**
	 * Fetch all food and order information for a given vendor in the basket
	 */
	public function getFoodsPerVendorInBasket($basket_id, $vendor_id){
		$query = $this->db->query('
			select
				f.id food_id, f.name, f.alternate_name, f.price, f.prep_time_hours,
				o.id order_id, o.quantity,
				p.path
			from
				order_item o
			left join
				food f
			on f.id = o.food_id
			left join
				food_picture p
			on p.food_id = f.id
			where
				o.order_basket_id = ?
				and f.user_id = ?
			group by f.id',
			array(
				$basket_id,
				$vendor_id
			)
		);
		
		return $query->result();
	}
	
	
	/**
	 * Fetch the total number of order items in the basket
	 * @return false if failed
	 */
	public function getTotalOrdersInBasket($basket_id){
		$query = $this->db->query('
			select sum(o.quantity) total
			from order_item o
			where o.order_basket_id = ?', array($basket_id));
		$results = $query->result();
		
		if (count($results)==0)
			return false;
		else
			return $results[0]->total;
	}
}