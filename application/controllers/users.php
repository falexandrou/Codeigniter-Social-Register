<?php
/******
* Codeigniter Social Register/Auth Sub system
* @author Fotis Alexandrou - fotis@redmark.gr
* @version 0.1
* @license Free/Open source
* http://www.redmark.gr
* Please read README.txt first
* It requires additional work IT IS NOT plug-n play
******/
class Users extends CI_Controller {

    /**
     * Constructor - Access Codeigniter's controller object
     * 
     */
    function __construct() {
	parent::__construct();
	//Load the session library - If session lib is autoloaded remove this from here
	$this->load->library('session');
	//Load the user helper - If the helper is autoloaded remove this from here
	$this->load->helper('user');
	$this->load->helper('url');
	//Load the user model
	$this->load->model('user');
    }

    /**
     * Placeholder for default functions to be executed
     */
    public function index() {
	if (!is_logged()) {
	    redirect('users/login');
	}
	//Get the user id
	//Load a home view
	$user = get_user();
	
	$this->load->view('users/home', array('user'=>$user));
    }

    /**
     * Displays the login screen
     */
    public function login() {
	$this->load->library('fb');
	$this->load->library('tweet');
	
	//Look for errors from previous steps
	$error = $this->session->flashdata('register_error');
	$this->load->view('users/login', array('fb' => $this->fb, 'twitter' => $this->tweet, 'error' => $error));
    }

    /**
     * Shows user's register screen
     */
    public function register() {
	$method = $this->uri->rsegment(3);
	
	//Gets the error from previous steps
	$error = $this->session->flashdata('register_error');

	if ($this->input->post('submit') != null) {
	    $name = $this->input->post('real_name');

	    $user_id = $this->user->add($name, $method);

	    if ($user_id != false) {
		if ($method == 'twitter') {
		    $handle = $this->input->post('handle');
		    $bio = $this->input->post('bio');
		    $profile_image_url = $this->input->post('profile_image_url');
		    $tw_id = $this->input->post('tw_id');
		    $this->user->store_twitter($tw_id, $handle, $real_name, $bio, $profile_image_url);
		    $this->_login($user_id, $method);
		    redirect('users/');
		} else if ($method == 'facebook') {
		    $email = $this->input->post('email');
		    $fb_id = $this->input->post('fb_id');
		    $bio = $this->input->post('bio');
		    $profile_image_url = $this->input->post('profile_image_url');
		    $handle = $this->input->post('handle');
		    $this->user->store_facebook($fb_id, $email, $profile_image_url, $bio, $handle);
		    $this->_login($user_id, $method);
		    redirect('users/');
		} else {
		    $error = 'You must select a valid login method';
		}
	    } else {
		$error = 'Registration failed please try again';
	    }
	}

	if ($method == 'twitter') {
	    //Form'submitted - TODO: Insert form validation
	    $this->load->library('tweet');
	    if (!$this->tweet->logged_in()) {
		$this->tweet->set_callback(current_url());
		$this->tweet->login();
		return;
	    }

	    $user = $this->tweet->call('get', 'account/verify_credentials');
	    $this->load->view('users/register_twitter', array('user' => $user, 'error' => $error));
	    return;
	} else if ($method == 'facebook') {
	    $this->load->library('fb');
	    if (!$this->fb->is_connected()){
		redirect ( $this->fb->login_url(array('next'=> current_url())));
	    }

	    $user = $this->fb->client->api('/me');
	    //Image on Graph API is loaded separately
	    $image = $this->fb->image_url();
	    $this->load->view('users/register_facebook', array('user' => $user, 'image'=>$image, 'error' => $error));
	    return;
	}
	redirect('users/login');
    }

    /**
     * Logs user in with facebook
     */
    public function facebook() {
	$this->load->library('fb');
	if (!$this->fb->is_connected()){
	    redirect ( $this->fb->login_url( current_url() ));
	}
	
	$fb_user = $this->fb->client->api('/me');
	
	if (empty($fb_user)){
	    $error = "FACEBOOK LOGIN FAILED - USER US EMPTY. FILE: " . __FILE__ . " LINE: " . __LINE__;
	    $this->session->set_flashdata('register_error', $error);
	}else{
	    $this->user->set_facebook_id($fb_user['id']);
	    $user = $this->user->get_by_facebook();
	    if (!empty($user) && !empty($user->id) && is_numeric($user->id)){
	    	//TODO: Make things a bit more secure here
		//Login & Redirect home
		$this->_login($user->id, 'facebook');
		$this->load->view('users/redirect_home');
		return;
	    }
	}
	//Go to the registeration page
	$this->load->view('users/redirect', array('method' => 'facebook'));
    }

    /**
     * Logs user in with twitter
     */
    public function twitter() {
	$this->load->library('tweet');
	if (!$this->tweet->logged_in()) {
	    $this->tweet->set_callback(current_url());
	    $this->tweet->login();
	    return;
	}

	$tw_user = $this->tweet->call('get', 'account/verify_credentials');
	if (empty($tw_user)) {
	    #TODO: Localize error messages etc
	    $error = 'TWITTER LOGIN FAILED - USER IS EMPTY FILE: ' . __FILE__ . ' LINE: ' . __LINE__;
	    $this->session->set_flashdata('register_error', $error);
	}else{
	    $this->user->set_twitter_id($tw_user->id_str);
	    $user = $this->user->get_by_twitter();
	    if (!empty($user) && !empty($user->id) && is_numeric($user->id)){
	    	//TODO: Make things a bit more secure here
		$this->_login($user->id, 'twitter');
		$this->load->view('users/redirect_home');
	    }
	}
	//Go to the registeration page
	$this->load->view('users/redirect', array('method' => 'twitter'));
    }

    /**
     * Stores a session variable
     */
    private function _login($user_id, $login_type) {
	//NOTE: Maybe a little workaround here, in order to make things more secure etc.
	$this->session->set_userdata('user_id', $user_id);
	$this->session->set_userdata('login_type', $login_type);
    }

    /**
     * Removes from session
     */
    public function logout() {
	$login_type = $this->session->userdata('login_type');
	$param = (int)$this->input->get('ret');
	if ($param != 1){
	    if ($login_type == 'facebook'){
		$this->_fb_logout();
	    }else if ($login_type == 'twitter'){
		$this->_tw_logout();
	    }
	}else{
	    $this->session->unset_userdata('user_id');
	    $this->session->unset_userdata('login_type');
	}
	redirect('/');
    }
    
    
    /**
     * Facebook has an issue with cookies unsetting, so...
     */
    private function _fb_logout() {

	$base_url = $this->config->item('base_url');

	if (array_key_exists('HTTP_REFERER', $_SERVER)) {
	    $refer = $_SERVER['HTTP_REFERER'];
	} else {
	    $refer = null;
	}

	$this->load->library('fb');
	$param = (int) $this->input->get('ret');
	//Local call
	if ($param != 1) {
	    //Store referer into a session variable
	    $url = $this->fb->logout_url(current_url() . '?ret=1');
	    redirect($url);
	} else {
	    $this->load->config('facebook');

	    $app_id = $this->config->item('facebook_app_id');
	    $api_key = $this->config->item('facebook_api_key');

	    $cookie_name = 'fbs_' . $app_id;

	    $dom = '.' . $_SERVER['HTTP_HOST'];

	    if (array_key_exists($cookie_name, $_COOKIE)) {
		setcookie($cookie_name, null, time() - 4200, '/', $dom);
		unset($_COOKIE[$cookie_name]);
	    }

	    if (array_key_exists($cookie_name, $_REQUEST)) {
		unset($_REQUEST[$cookie_name]);
	    }

	    $cookies = array($api_key . '_expires', $api_key . '_session_key', $api_key . '_ss', $api_key . '_user', $api_key, 'base_domain_' . $api_key);

	    foreach ($cookies as $var) {
		if (array_key_exists($var, $_COOKIE)) {
		    setcookie($var, null, time() - 4200, '/', $dom);
		    unset($_COOKIE[$var]);
		}

		if (array_key_exists($var, $_REQUEST)) {
		    unset($_REQUEST[$var]);
		}
	    }

	    if ($goto == null || $goto == '') {
		$goto = site_url('/');
	    }

	    $session = $this->fb->client->getSession();

	    redirect($goto);
	}
    }
    
    /**
     * Logout from twitter
     */
    private function _tw_logout(){
	$this->load->library('tweet');
	$this->tweet->set_callback(current_url().'?ret=1');
	$this->tweet->logout();
    }

}