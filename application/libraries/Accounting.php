<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounting {
	/**
	 * Calculate the costs of a paid OrderItem
	 *
	 * @param $order_item, order_item object, must contain quantity, price, tax_rate and take_rate fields
	 * @return ["base_cost", "commission", "taxes"]
	 */
	public function calcPaidOrderItemCosts($order_item){
		$food_cost = $order_item->quantity*$order_item->price;
		$food_commission = $order_item->take_rate*$food_cost;
		$food_tax = ($food_cost+$food_commission)*$order_item->tax_rate;
		
		return array(
			"base_cost" 	=> $food_cost,
			"commission"	=> $food_commission,
			"taxes"			=> $food_tax
		);
	}
	
	
	/**
	 * Calculate the costs of an open OrderItem
	 *
	 * @param $order_item, order_item object, must contain quantity, price fields
	 * @param $config, $this->config
	 * @return ["base_cost", "commission", "taxes"]
	 */
	public function calcOpenOrderItemCosts($order_item, $config){
		$base_cost = $order_item->quantity*$order_item->price;
		$commission = $base_cost*$config->item('take_rate');
		$taxes = ($base_cost+$commission)*$config->item('tax_rate');
		
		return array(
			"base_cost" 	=> $base_cost,
			"commission"	=> $commission,
			"taxes"			=> $taxes
		);
	}
	
	
	/**
	 * Calculate the costs of an open order_basket
	 *
	 * @param $open_basket order_basket object
	 * @param $config, $this->config
	 * @return ["base_cost", "commission", "taxes"]
	 */
	public function calcOpenBasketCosts($open_basket_id, $config){
		$CI =& get_instance();
		$CI->load->model('order_basket_model');
		
		$base_cost = $CI->order_basket_model->getBaseCostInBasket($open_basket_id);
		if ($base_cost === false){
			throw new Exception("database error");
		}
		$commission = $base_cost*$config->item('take_rate');
		$taxes = ($base_cost+$commission)*$config->item('tax_rate');
		
		return array(
			"base_cost" 	=> $base_cost,
			"commission"	=> $commission,
			"taxes"			=> $taxes
		);
	}
	
	
	/**
	 * Get current take_rate and tax_rate
	 *
	 * @param $config, $this->config
	 * @return ["take_rate", "tax_rate"]
	 */
	public function getCurrentRates($config){
		return array(
			"take_rate"		=> $config->item("take_rate"),
			"tax_rate"		=> $config->item("tax_rate")
		);
	}
}