<?php

/**
 * Translate the preparation time returned from the database to
 * a format to be displayed in the view
 */
function prep_time_for_display($prep_time){
	if ($prep_time <= 1){
		return round($prep_time*60)."min";
	} else {
		return round($prep_time, 1)."hr";
	}
}


/**
 * From $text, return a string that contains the first $limit words
 */
function limit_text($text, $limit) {
	if (str_word_count($text, 0) > $limit) {
		$words = str_word_count($text, 2);
		$pos = array_keys($words);
		$text = substr($text, 0, $pos[$limit]). " ...";
	}
	return $text;
}


/**
 * Check whether the current request is POST
 */
function is_post_request(){
	return ($_SERVER['REQUEST_METHOD'] == "post" || $_SERVER['REQUEST_METHOD'] == "POST");
}


function send_to_google_maps($address, $city, $province, $postal_code){
	$str = $address." ".$city." ".$province." ".$postal_code;
	$str = str_replace(" ", "+", $str);
	return "https://www.google.ca/maps/place/$str";
}