<?php

/**
 * Use cURL to save a remote image to a local file
 * @param unknown $url URL for image retrieval
 * @param unknown $saveTo Location to save the image
 */
function save_remote_resource($url, $saveTo) {
	// use cURL
	$ch = curl_init ($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);
	
	file_put_contents($saveTo, $data);
}