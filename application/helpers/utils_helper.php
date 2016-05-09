<?php

/**
 * Translate the preparation time returned from the database to
 * a format to be displayed in the view
 */
function prepTimeForDisplay($prep_time){
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