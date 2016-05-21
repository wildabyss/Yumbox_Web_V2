<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Yumbox_Controller {
	protected function display_login($requestUrl, $error=false){
		// Initialize Facebook
		$fb_config = array(
			'app_id' => $this->config->item('facebook_app_id'),
			'app_secret' => $this->config->item('facebook_secret')
		);
		$fb = new Facebook\Facebook($fb_config);
		$fbHelper = $fb->getRedirectLoginHelper();
		
		// Initialize Google
		$client_id = $this->config->item('google_client_id');
		$client_secret = $this->config->item('google_client_secret');
		$google = new Google_Client();
		$google->setClientId($client_id);
		$google->setClientSecret($client_secret);
		
		// Set up Facebook login button
		$permissions = $this->login_util->facebook_permissions();
		$redirectUrl = $this->login_util->facebook_redirect_url($this->config->item('base_url'), $requestUrl);
		$fbLoginUrl = $fbHelper->getLoginUrl($redirectUrl, $permissions);
		
		// Set up Google login button
		$scopes = $this->login_util->google_scopes();
		$redirectUrl = $this->login_util->google_redirect_url($this->config->item('base_url'));
		$google->setRedirectUri($redirectUrl);
		$google->setScopes($scopes);
		$googleLoginUrl = $google->createAuthUrl()."&state=".urlencode($requestUrl);
		
		// bind to data
		$data['fb_login_url'] = $fbLoginUrl;
		$data['google_login_url'] = $googleLoginUrl;
		if ($error !== false)
			$data['error'] = $error;
		
		// Load views
		$this->header();
		$this->navigation();
		$this->load->view("login", $data);
		$this->footer();
	}
	
	public function facebook(){
		// get redirect URL
		$requestUrl = $this->input->get("redirect", true);
		
		// perform Facebook login
		$res = false;
		try {
			$res = $this->login_util->loginFacebook($this->config->item('facebook_app_id'), $this->config->item('facebook_secret'), $requestUrl);
		} catch (Exception $e){
			$this->display_login($requestUrl, $e->getMessage());
			return;
		}
		
		if ($res){
			// redirect to the request URL
			redirect($requestUrl, 'refresh');
		} else {
			// If we reached here, then login has failed
			$this->display_login($requestUrl);
		}
	}
	
	public function google(){
		// for Google, the request URL is set in the state parameter
		$requestUrl = $this->input->get("state", true);
		
		// perform Google login
		$res = false;
		try {
			$res = $this->login_util->loginGoogle($this->config->item('google_client_id'), 
				$this->config->item('google_client_secret'), $this->config->item('base_url'), $requestUrl);
		} catch (Exception $e){
			$this->display_login($requestUrl, $e->getMessage());
			return;
		}
		
		if ($res){
			// redirect to the request URL
			redirect($requestUrl, 'refresh');
		} else {
			// If we reached here, then login has failed
			$this->display_login($requestUrl);
		}
	}

	public function index()
	{
		$requestUrl = $this->input->get("redirect", true);
		
		if ($this->login_util->isUserLoggedIn()){
			// session exists
			redirect($requestUrl, 'refresh');
		}
		
		$this->display_login($requestUrl);
	}
}
