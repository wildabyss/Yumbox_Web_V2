<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Customer_Controller {
	//protected function login
	
	public function redirect($requestUrl="menu"){
		if (isset($_SESSION['fb_token']) || isset($_SESSION['google_token'])){
			// session exists
			redirect($requestUrl, 'refresh');
		}
		
		// Initialize Facebook
		$fb_config = array(
			'app_id' => $this->config->item('facebook_app_id'),
			'app_secret' => $this->config->item('facebook_secret')
		);
		$fb = new Facebook\Facebook($fb_config);
		$fbHelper = $fb->getRedirectLoginHelper();
		
		//-- Attempt Facebook login first
		
		// Try to get the access token
		try {
			$accessToken = $fbHelper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$error = "Facebook Graph error";
			goto NO_LOGIN;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$error = "Facebook validation error";
			goto NO_LOGIN;
		}
		
		// Proceed to retrieve Facebook ID and user name
		try {
			$response = $fb->get('/me?fields=id,name,email', $accessToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			$error = "Facebook Graph error";
			goto NO_LOGIN;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$error = "Facebook validation error";
			goto NO_LOGIN;
		}
		$user = $response->getGraphUser();
		$fbId = $user['id'];
		$fbName = $user['name'];
		$email = $user['email'];
		
		// fetch user object from the database
		if ($this->user_model->getUserForFacebookId($fbId) == NULL){
			// If it doesn't exist in the db, add the user
			$this->user_model->addUser(User_model::$CUSTOMER, $fbName, $email, $fbId);
		}
		
		// successful retrieval of token
		$_SESSION['fb_token'] = $accessToken;
		
		redirect($requestUrl, 'refresh');
		
		//-- END OF FACEBOOK LOGIN
		
		//-- Attempt Google login
		
		//-- END OF GOOGLE LOGIN
		
		NO_LOGIN:
		
		// If we reached here, then we need to display the login screen
		$redirectUrl = "http://$_SERVER[HTTP_HOST]/login/redirect/".urlencode($requestUrl);
		$permissions = ['email'];
		$fbLoginUrl = $fbHelper->getLoginUrl($redirectUrl, $permissions);
		
		// bind to data
		$data['fb_login_url'] = $fbLoginUrl;
		if (isset($error))
			$data['error'] = $error;
		
		// Load views
		$this->header();
		$this->load->view("customer/login", $data);
		$this->footer();
	}

	public function index()
	{
		$this->redirect();
	}
}
