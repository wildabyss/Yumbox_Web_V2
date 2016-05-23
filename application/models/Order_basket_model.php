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
				b.id, b.order_date, b.is_paid
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
	 * Fetch all order_baskets that have been paid for user_id
	 */
	public function getPaidOrderBasketsForUser($user_id){
		$query = $this->db->query('
			select
				b.id, b.order_date, b.is_paid,
				sum(f.price*o.quantity) total_cost,
				min(o.is_filled) is_filled
			from
				order_basket b
			left join
				order_item o
			on o.order_basket_id = b.id
			left join
				food f
			on f.id = o.food_id
			where
				b.user_id = ?
				and b.is_paid = 1
			group by b.id', array($user_id));
		return $query->result();
	}
	
	
	/**
	 * Fetch the order_basket for a given $user_id that hasn't finished checkout
	 * @return false if not exist
	 */
	protected function getOpenBasketForUser($user_id){
		$query = $this->db->query('
			select
				b.id, b.order_date, b.delivery_address
			from
				order_basket b
			where
				b.is_paid = 0
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
	protected function createOpenBasketForUser($user_id){
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
	 * Fetch all order_items in basket
	 */
	public function getAllOrderItemsInBasket($basket_id){
		$query = $this->db->query('
			select
				o.id order_id, o.quantity,
				f.id food_id, f.price,
				p.id payment_id, r.id refund_id,
				f.user_id vendor_id
			from
				order_item o
			left join
				food f
			on f.id = o.food_id
			left join
				payment p
			on p.order_item_id = o.id
			left join
				refund r
			on r.order_item_id = o.id
			where
				order_basket_id = ?', array($basket_id));
		return $query->result();
	}
	
	
	/**
	 * Fetch all vendors in the order basket
	 */
	public function getAllVendorsInBasket($basket_id){
		$query = $this->db->query('
			select
				u.id, u.name, u.is_open, u.email, u.descr,
				a.address, a.city, a.province, a.postal_code, a.country, a.latitude, a.longitude
			from
				order_item o
			left join food f
			on f.id = o.food_id
			left join user u
			on u.id = f.user_id
			left join address a
			on a.user_id = u.id
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
				o.id order_id, o.quantity, o.is_filled, r.id refund_id, 
				p.id payment_id, p.tax_rate, p.take_rate,
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
				payment p
			on p.order_item_id = o.id
			left join
				refund r
			on r.order_item_id = o.id
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
			return $results[0]->total==""?0:$results[0]->total;
	}
	
	
	/**
	 * Fetch the total base cost in the order_basket
	 */
	public function getBaseCostInBasket($basket_id){
		$query = $this->db->query('
			select 
				sum(f.price*o.quantity) total
			from 
				order_item o
			left join food f
			on f.id = o.food_id
			left join refund r
			on r.order_item_id = o.id
			where 
				o.order_basket_id = ?
				and r.id is null
			group by 
				o.order_basket_id', array($basket_id));
		$results = $query->result();
		
		if (count($results)==0)
			return 0;
		else
			return $results[0]->total==""?0:$results[0]->total;
	}
	
	
	/**
	 * Fetch the total cost paid in the basket
	 * Note: basket must have been paid already (i.e. not open)
	 */
	public function getTotalCostInPaidBasket($basket_id){
		$query = $this->db->query('
			select 
				sum(p.amount) total
			from 
				order_item o
			left join food f
			on f.id = o.food_id
			left join refund r
			on r.order_item_id = o.id
			left join payment p
			on p.order_item_id = o.id
			where 
				o.order_basket_id = ?
				and r.id is null
			group by 
				o.order_basket_id', array($basket_id));
		$results = $query->result();
		
		if (count($results)==0)
			return 0;
		else
			return $results[0]->total==""?0:$results[0]->total;
	}
	
	
	/**
	 * Add a single order to the $order_basket_id
	 * @return true on success, error on failure
	 */
	public function addOrderToBasket($food_id, $order_basket_id){
		if (!$this->db->query('call add_order(?,?,?)', 
			array($order_basket_id, $food_id, 1))){
			
			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Remove order from $order_basket_id
	 * @return true on success, error on failure
	 */
	public function removeOrderFromBasket($order_id, $order_basket_id){
		if (!$this->db->query('
			delete from order_item
			where
				order_basket_id = ?
				and id = ?', array($order_basket_id, $order_id))){

			return $this->db->error();
		}
		
		return true;
	}
	
	
	/**
	 * Set is_paid bit to true for the order_basket
	 * @return true for success, error on failure
	 */
	public function setBasketAsPaid($order_basket_id){
		if (!$this->db->query('
			update order_basket
			set 
				is_paid = 1,
				order_date = now()
			where
				id = ?', array($order_basket_id))){

			return $this->db->error();
		}
		
		return true;
	}
}