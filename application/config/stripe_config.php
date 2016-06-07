<?php
 /**
  * The secure-free configuration related to Stripe
  */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['stripe'] = array(
	'default_country' => 'CA',
	'default_currency' => 'cad',
	'default_account_type' => 'individual', // individual / company
	'default_state' => 'ON',
	'default_city' => 'Toronto',
	'default_account_holder_type' => 'individual', // individual / company
);
