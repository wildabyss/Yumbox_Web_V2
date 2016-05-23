<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Time_prediction {
	public static $RUSH_HOUR_CUTOFF = 2;	// maximum rush hour cutoff time
	
	/**
	 * Calculate the predicted pickup time for food and time of order
	 * @param $order_time PHP time for order (timestamp)
	 * @return hours if $return_elapsed = true, PHP time if false
	 */
	public function calcPickupTime($food_id, $order_time, $return_elapsed = true){
		// initialize models
		$CI =& get_instance();
		$CI->load->model('user_model');
		
		// get food information
		$food = $CI->food_model->getFoodPickupTimesForFoodId($food_id);
		
		$prep_time_hours = $food->prep_time_hours;
		if ($food->pickup_method == Food_model::$PICKUP_ANYTIME){
			// pick up from order time + prep_time_hours
			
			if ($return_elapsed){
				return $prep_time_hours;
			} else {
				$pickup_time = $order_time + $prep_time_hours*3600;
				return $pickup_time;
			}
			
		} else {
			// pick up at designated pickup times
			
			$slots = array(
				$food->pickup_mon,
				$food->pickup_tue,
				$food->pickup_wed,
				$food->pickup_thu,
				$food->pickup_fri,
				$food->pickup_sat,
				$food->pickup_sun
			);
			$weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
			
			// get the weekday the order is made
			$order_weekday = date("N", $order_time) - 1;
			
			// get next day
			$time_diff = 100*24;
			$pickup_time = 0;
			for ($i=0; $i<7; $i++){
				if ($slots[$i] != "00:00:00"){
					$str = "{$weekdays[$i]} {$slots[$i]}";
					$next_time = strtotime($str, $order_time);

					$startObj = new DateTime();
					$startObj->setTimestamp($next_time);
					$endObj = new DateTime();
					$endObj->setTimestamp($order_time);
					$diffObj = $startObj->diff($endObj);
					$diff = $diffObj->d*24 + $diffObj->h + $diffObj->m/60;
					
					if ($diff < $time_diff && $diff >= $prep_time_hours){
						$time_diff = $diff;
						$pickup_time = $next_time;
					}
				}
			}
			
			if ($return_elapsed){
				return $time_diff;
			} else {
				return $pickup_time;
			}
		}
	}
}
