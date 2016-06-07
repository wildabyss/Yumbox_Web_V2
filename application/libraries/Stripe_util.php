<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Stripe_util
 */
class Stripe_util {
	/**
	 * Checks and return whether a Stripe's managed account is
	 * capable of receiving charges and transferring payouts.
	 * @param $managed_account_id
	 * @return array
	 */
	public function isAccountPayable($managed_account_id) {
		$CI =& get_instance();

		\Stripe\Stripe::setApiKey($CI->config->item("stripe_secret_key"));
		$account = \Stripe\Account::retrieve($managed_account_id);

		return array(
			'charges_enabled' => $account->charges_enabled,
			'transfers_enabled' => $account->transfers_enabled,
		);
	}
}