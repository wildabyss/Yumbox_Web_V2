<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_util {
	/**
	 * Send welcome email for newly registered users
	 */
	protected function sendWelcomeEmail() {
		$CI =& get_instance();
		$CI->load->model('user_model');

		$user_id = $this->getUserId();
		$user = $CI->user_model->getUserForUserId($user_id);

		$mustache = new Mustache_Engine();
		$CI->config->load('secret_config', TRUE);

		//TODO: Do we really want to load email templates according to current language?
		$CI->lang->load('email');
		$subject = $mustache->render($CI->lang->line('sign_up_subject'), array(
			'user' => $user,
			'base_url' => $CI->config->item('base_url', 'secret_config'),
		));
		$body = $mustache->render($CI->lang->line('sign_up_body'), array(
			'user' => $user,
			'base_url' => $CI->config->item('base_url', 'secret_config'),
		));

		// Sending email to customer
		$CI->load->library('mail_server');
		$CI->mail_server->sendFromWebsite($user->email, $user->name, $subject, $body);
	}
	
	
	/**
	 * Invalidate the location cookies
	 */
	protected function invalidateLocation(){
		delete_cookie("latitude");
		delete_cookie("longitude");
	}
	
	
	/**
	 * Return true if the user has logged in
	 */
	public function isUserLoggedIn()
	{
		if (isset($_SESSION['user_id']) 
			&& (isset($_SESSION['fb_token']) || isset($_SESSION['google_token']))){
			// session exists
			return true;
		} else{
			return false;
		}
	}
	
	/**
	 * Return user_id if it exists
	 * False otherwise
	 */
	public function getUserId(){
		if ($this->isUserLoggedIn())
			return $_SESSION['user_id'];
		else
			return false;
	}
	
	
	/**
	 * Utiliy method
	 */
	public function facebook_redirect_url($base_url, $requestUrl){
		return $base_url."login/facebook?redirect=".urlencode($requestUrl);
	}
	
	/**
	 * Utiliy method
	 */
	public function facebook_permissions(){
		return array('email');
	}
	
	/**
	 * Utiliy method
	 */
	public function google_redirect_url($base_url){
		// the requestUrl will be passed to the state parameter
		return $base_url."login/google";
	}
	
	/**
	 * Utiliy method
	 */
	public function google_scopes(){
		return array('profile', 'email');
	}
	
	
	/**
	 * Perform Facebook login
	 * If user is not registered, register the user
	 */
	public function loginFacebook($app_id, $app_secret, $requestUrl){
		// initialize models
		$CI =& get_instance();
		$CI->load->model('user_model');
		
		// Initialize Facebook
		$fb_config = array(
			'app_id' => $app_id,
			'app_secret' => $app_secret
		);
		$fb = new Facebook\Facebook($fb_config);
		$fbHelper = $fb->getRedirectLoginHelper();
			
		// Try to get the access token
		try {
			$accessToken = $fbHelper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$error = "Facebook Graph error";
			throw new Exception($error);
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$error = "Facebook validation error";
			throw new Exception($error);
		}
		
		// Proceed to retrieve Facebook ID and user name
		if (isset($accessToken)){
			try {
				$response = $fb->get('/me?fields=id,name,email', $accessToken);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				$error = "Facebook Graph error";
				throw new Exception($error);
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				$error = "Facebook validation error";
				throw new Exception($error);
			}
			$user = $response->getGraphUser();
			$fbId = $user['id'];
			$name = $user['name'];
			$email = $user['email'];

			$welcomeEmail = false;

			// fetch user object from the database
			if ($CI->user_model->getUserForFacebookId($fbId) === false){
				// If it doesn't exist in the db, add the user
				if ($CI->user_model->addUser(User_model::$CUSTOMER, $name, $email, $fbId) !== true){
					$error = "Internal server error";
					throw new Exception($error);
				}
				
				// send welcome email at the end
				$welcomeEmail = true;
			}
			
			// user object
			$user = $CI->user_model->getUserForFacebookId($fbId);
			
			// add address
			$address = $CI->user_model->getOrCreateAddress($user->id);
			
			// successful retrieval of token
			$_SESSION['fb_token'] = $accessToken;
			$_SESSION['user_id'] = $user->id;
			
			// invalidate cached location
			$this->invalidateLocation();

			// send welcome email
			if ($welcomeEmail) {
				$this->sendWelcomeEmail();
			}

			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Perform Google oAuth2 login
	 * If user is not registered, register the user
	 */
	public function loginGoogle($client_id, $client_secret, $base_url, $requestUrl){
		// initialize models
		$CI =& get_instance();
		$CI->load->model('user_model');
		
		// Initialize Google
		$google = new Google_Client();
		$google->setClientId($client_id);
		$google->setClientSecret($client_secret);
		$google->setScopes($this->google_scopes());
		$redirectUrl = $this->google_redirect_url($base_url);
		$google->setRedirectUri($redirectUrl);
		
		if (isset($_GET['error'])){
			$error = "Google login error";
			throw new Exception($error);
		}
		
		if (isset($_GET['code'])){
			// get access token
			try{
				$google->authenticate($_GET['code']);
				$accessToken = $google->getAccessToken();
			} catch (Exception $e){
				$error = "Google token error";
				throw new Exception($error);
			}
			
			// Get user info from Google Plus
			$googleService = new Google_Service_Plus($google);
			$user = $googleService->people->get("me");
			$googleId = $user->id;
			$name = $user->displayName;
			$email = $user->emails[0]['value'];

			$welcomeEmail = false;

			// fetch user object from the database
			if ($CI->user_model->getUserForGoogleId($googleId) === false){
				// If it doesn't exist in the db, add the user
				if ($CI->user_model->addUser(User_model::$CUSTOMER, $name, $email, NULL, $googleId) !== true){
					$error = "Internal server error";
					throw new Exception($error);
				}
				else {
					$welcomeEmail = true;
				}
			}
			
			// user object
			$user = $CI->user_model->getUserForGoogleId($googleId);
			
			// add address
			$address = $CI->user_model->getOrCreateAddress($user->id);

			// successful retrieval of token
			$_SESSION['google_token'] = $accessToken;
			$_SESSION['user_id'] = $user->id;
			
			// invalidate cached location
			$this->invalidateLocation();

			// send welcome email
			if ($welcomeEmail) {
				$this->sendWelcomeEmail();
			}

			return true;
		} else {
			return false;
		}
	}
	
	
	public function logout(){
		$CI =& get_instance();
		$CI->load->library('session');
		
		// destroy session
		$CI->session->sess_destroy();
		
		// invalidate cached locations
		$this->invalidateLocation();
	}
}