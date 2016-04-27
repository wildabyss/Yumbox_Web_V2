<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Customer_Controller {
	protected function display_login($requestUrl, $error=NULL){
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
		$permissions = $this->facebook_permissions();
		$redirectUrl = $this->facebook_redirect_url($requestUrl);
		$fbLoginUrl = $fbHelper->getLoginUrl($redirectUrl, $permissions);
		
		// Set up Google login button
		$scopes = $this->google_scopes();
		$redirectUrl = $this->google_redirect_url($requestUrl);
		$google->setRedirectUri($redirectUrl);
		$google->setScopes($scopes);
		$googleLoginUrl = $google->createAuthUrl();
		
		// bind to data
		$data['fb_login_url'] = $fbLoginUrl;
		$data['google_login_url'] = $googleLoginUrl;
		if (isset($error))
			$data['error'] = $error;
		
		// Load views
		$this->header();
		$this->load->view("login", $data);
		$this->footer();
	}
	
	protected function facebook_redirect_url($requestUrl){
		return $this->config->item('base_url')."login/facebook/".urlencode($requestUrl);
	}
	
	protected function facebook_permissions(){
		return array('email');
	}
	
	protected function google_redirect_url($requestUrl){
		return $this->config->item('base_url')."login/google/".urlencode($requestUrl);
	}
	
	protected function google_scopes(){
		return array('profile', 'email');
	}
	
	public function facebook($requestUrl="menu"){
		// Initialize Facebook
		$fb_config = array(
			'app_id' => $this->config->item('facebook_app_id'),
			'app_secret' => $this->config->item('facebook_secret')
		);
		$fb = new Facebook\Facebook($fb_config);
		$fbHelper = $fb->getRedirectLoginHelper();
			
		// Try to get the access token
		try {
			$accessToken = $fbHelper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$error = "Facebook Graph error";
			$this->display_login($requestUrl, $error);
			return;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$error = "Facebook validation error";
			$this->display_login($requestUrl, $error);
			return;
		}
		
		// Proceed to retrieve Facebook ID and user name
		if (isset($accessToken)){
			try {
				$response = $fb->get('/me?fields=id,name,email', $accessToken);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				$error = "Facebook Graph error";
				$this->display_login($requestUrl, $error);
				return;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				$error = "Facebook validation error";
				$this->display_login($requestUrl, $error);
				return;
			}
			$user = $response->getGraphUser();
			$fbId = $user['id'];
			$name = $user['name'];
			$email = $user['email'];
			
			// fetch user object from the database
			if ($this->user_model->getUserForFacebookId($fbId) == NULL){
				// If it doesn't exist in the db, add the user
				if ($this->user_model->addUser(User_model::$CUSTOMER, $name, $email, $fbId) !== true){
					$error = "Internal server error";
					$this->display_login($requestUrl, $error);
					return;
				}
			}
			
			// user object
			$user = $this->user_model->getUserForFacebookId($fbId);
			
			// successful retrieval of token
			$_SESSION['fb_token'] = $accessToken;
			$_SESSION['user_id'] = $user->id;
			redirect($requestUrl, 'refresh');
			return;
		}
		
		// If we reached here, then login has failed
		$this->display_login($requestUrl);
	}
	
	public function google($requestUrl="menu"){
		// Initialize Google
		$client_id = $this->config->item('google_client_id');
		$client_secret = $this->config->item('google_client_secret');
		$google = new Google_Client();
		$google->setClientId($client_id);
		$google->setClientSecret($client_secret);
		$google->setScopes($this->google_scopes());
		$redirectUrl = $this->google_redirect_url($requestUrl);
		$google->setRedirectUri($redirectUrl);
		
		if (isset($_GET['error'])){
			$error = "Google login error";
			$this->display_login($requestUrl, $error);
			return;
		}
		
		if (isset($_GET['code'])){
			// get access token
			try{
				$google->authenticate($_GET['code']);
				$accessToken = $google->getAccessToken();
			} catch (Exception $e){
				$error = "Google token error";
				$this->display_login($requestUrl, $error);
				return;
			}
			
			// Get user info from Google Plus
			$googleService = new Google_Service_Plus($google);
			$user = $googleService->people->get("me");
			$googleId = $user->id;
			$name = $user->displayName;
			$email = $user->emails[0]['value'];
			
			// fetch user object from the database
			if ($this->user_model->getUserForGoogleId($googleId) == NULL){
				// If it doesn't exist in the db, add the user
				if ($this->user_model->addUser(User_model::$CUSTOMER, $name, $email, NULL, $googleId) !== true){
					$error = "Internal server error";
					$this->display_login($requestUrl, $error);
					return;
				}
			}
			
			// user object
			$user = $this->user_model->getUserForGoogleId($googleId);

			// successful retrieval of token
			$_SESSION['google_token'] = $accessToken;
			$_SESSION['user_id'] = $user->id;
			redirect($requestUrl, 'refresh');
			return;
		}
		
		// If we reached here, then login has failed
		$this->display_login($requestUrl);
	}

	public function index($requestUrl="menu")
	{
		if (isset($_SESSION['fb_token']) || isset($_SESSION['google_token'])){
			// session exists
			redirect($requestUrl, 'refresh');
		}
		
		$this->display_login($requestUrl);
	}
}
