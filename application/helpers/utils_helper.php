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


/**
 * HTML encode the given output string to prevent XSS
 */
function prevent_xss($output){
	return htmlspecialchars($output, ENT_QUOTES);
}


/**
 * Create an external Google Map URL for the provided address
 */
function send_to_google_maps($address, $city, $province, $postal_code){
	$str = $address." ".$city." ".$province." ".$postal_code;
	$str = str_replace(" ", "+", $str);
	return "https://www.google.ca/maps/place/$str";
}

/**
 * translates a filename from `image.jpg` to `image_jpg`
 *
 * @param $filename
 * @return mixed
 */
function prep_food_image_filename($filename)
{
	if (is_string($filename)) {
		$pos = strrpos($filename, '.');
		if ($pos !== false) {
			$filename = substr_replace($filename, '_', $pos, strlen('.'));
		}
		return $filename;
	}
	else {
		return $filename;
	}
}

/**
 * this helper function sends a cached version of an image to web server's output buffer
 * it will create the resized image file if it does not exist
 *
 * @param $original_path
 * @param $width
 * @param $height
 */
function download_cached_image($original_path, $width, $height)
{
	$original_info = pathinfo($original_path);
	$filename = $original_info['filename'];

	//Requested filename is `image_jpg` instead of `image.jpg`, let's revert it
	$pos = strrpos($filename, '_');
	if ($pos !== false) {
		$filename = substr_replace($filename, '.', $pos, strlen('_'));
		$original_path = $original_info['dirname'] . DIRECTORY_SEPARATOR . $filename;
	}

	//Act only the original file exists
	if (file_exists($original_path)) {
		//Validate width and height
		if ($width <= 0 || $height <= 0) {
			$image = new \Eventviva\ImageResize($original_path);
			$width = $image->getSourceWidth();
			$height = $image->getSourceHeight();
		}
		//Making sure no one can ask for an image bigger than 1440 * 1440
		$width = $width > 1440 ? 1440 : $width;
		$height = $height > 1440 ? 1440 : $height;

		//Cache folder to save resized images
		$cache_folder = $original_info['dirname'] . DIRECTORY_SEPARATOR . 'cache';
		if (!file_exists($cache_folder)) {
			mkdir($cache_folder);
		}
		$info = pathinfo($filename);
		$image_path = $cache_folder . DIRECTORY_SEPARATOR . $info['filename'] . "_{$width}_{$height}." . $info['extension'];
		//Check and see if the requested size has already been created and cached
		if (!file_exists($image_path)) {
			//Resize the image to match the requested size
			if (!isset($image)) {
				$image = new \Eventviva\ImageResize($original_path);
			}
			$scaleW = $width / $image->getSourceWidth();
			$scaleH = $height / $image->getSourceHeight();
			//Scale the image to fit the biggest edge
			$image->scale(($scaleW > $scaleH ? $scaleW : $scaleH) * 100.0);
			$image->save($image_path);
		}

		//Download the resized image
		header('Content-Length: '.filesize($image_path)); //<-- sends file size header
		header('Content-Type: '.mime_content_type($image_path)); //<-- send mime-type header
		header('Content-Disposition: inline; filename="'.$filename.'";'); //<-- sends filename header
		readfile($image_path); //<--reads and outputs the file onto the output buffer
		die(); //<--cleanup
		exit; //and exit
	}
	else {
		show_404();
	}
}

function unlinkImageAndCache($file_path)
{
	$info = pathinfo($file_path);
	@unlink($file_path);
	$pattern = $info['dirname'] . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $info['filename'] . '*';
	foreach (glob($pattern) as $filename) {
		@unlink($filename);
	}
}