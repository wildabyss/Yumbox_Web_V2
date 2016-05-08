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