<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Time_prediction {
	public static $RUSH_HOUR_CUTOFF = 2;	// maximum rush hour cutoff time
	
	/**
	 * Calculate the predicted pickup time for food and time of order
	 * @param $order_time PHP time for order (timestamp)
	 * @return hours till pickup
	 */
	public function calcPickupTime($food_id, $order_time){
		// initialize models
		$CI =& get_instance();
		$CI->load->model('user_model');
		
		// get food information
		$food = $CI->food_model->getFoodPickupTimesForFoodId($food_id);
		
		$prep_time_hours = $food->prep_time_hours;
		if ($food->pickup_method == Food_model::$PICKUP_ANYTIME){
			// pick up from order time + prep_time_hours
			
			
			
		} else {
			// pick up at designated pickup times
			
			
		}
	}
}