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
	 * @return ["base_cost", "commission", "taxes"]
	 */
	public function calcOpenOrderItemCosts($order_item){
		$CI =& get_instance();
		$CI->config->load('config', TRUE);
		
		$base_cost = $order_item->quantity*$order_item->price;
		$commission = $base_cost*$CI->config->item('take_rate');
		$taxes = ($base_cost+$commission)*$CI->config->item('tax_rate');
		
		return array(
			"base_cost" 	=> $base_cost,
			"commission"	=> $commission,
			"taxes"			=> $taxes
		);
	}
	
	
	/**
	 * For a given total item cost, calculate the share to be dispensed to vendor
	 * and us
	 * @return ["vendor_share", "application_share"]
	 */
	public function calcOpenOrderItemVendorShare($total_cost){
		$CI =& get_instance();
		$CI->config->load('config', TRUE);
		
		$vendor_take_rate = $CI->config->item("take_rate_vendor");
		$vendor_share = $vendor_take_rate*$total_cost;
		$application_share = $total_cost-$vendor_share;
		
		return array(
			"vendor_share"		=> $vendor_share,
			"application_share"	=> $application_share
		);
	}
	
	
	/**
	 * Calculate the costs of an open order_basket
	 *
	 * @param $open_basket order_basket object
	 * @return ["base_cost", "commission", "taxes"]
	 */
	public function calcOpenBasketCosts($open_basket_id){
		$CI =& get_instance();
		$CI->load->model('order_basket_model');
		$CI->config->load('config', TRUE);
		
		$base_cost = $CI->order_basket_model->getBaseCostInBasket($open_basket_id);
		if ($base_cost === false){
			throw new Exception("database error");
		}
		$commission = $base_cost*$CI->config->item('take_rate');
		$taxes = ($base_cost+$commission)*$CI->config->item('tax_rate');
		
		return array(
			"base_cost" 	=> $base_cost,
			"commission"	=> $commission,
			"taxes"			=> $taxes
		);
	}
	
	
	/**
	 * Get current take_rate and tax_rate
	 *
	 * @return ["take_rate", "tax_rate", "vendor_take_rate"]
	 */
	public function getCurrentRates(){
		$CI =& get_instance();
		$CI->config->load('config', TRUE);
		
		return array(
			"take_rate"		=> $CI->config->item("take_rate"),
			"tax_rate"		=> $CI->config->item("tax_rate"),
			"vendor_take_rate"	=> $CI->config->item("take_rate_vendor")
		);
	}
}