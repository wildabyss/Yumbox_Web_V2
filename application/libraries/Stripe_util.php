<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Stripe_util
 */
class Stripe_util {
	/**
	 * Checks and return whether a Stripe's managed account is
	 * capable of receiving charges and transferring payouts.
	 * @param $user_id
	 * @return bool
	 */
	public function isAccountPayable($user_id) {
		$CI =& get_instance();
		$CI->load->model('user_model');
		
		$user = $CI->user_model->getUserForUserId($user_id);
		if ($user === false || $user->stripe_managed_account_id == "")
			return false;

		// fetch stripe info
		$account = false;
		try {
			$managed_account_id = $user->stripe_managed_account_id;
			\Stripe\Stripe::setApiKey($CI->config->item("stripe_secret_key"));
			$account = \Stripe\Account::retrieve($managed_account_id);
		} catch (Exception $e){
			return false;
		}

		return ($account !== false && $account->charges_enabled === true && $account->transfers_enabled === true);
	}
}