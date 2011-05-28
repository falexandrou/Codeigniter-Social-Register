<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

include_once APPPATH . 'libraries/fbapi/fbexception.php';
require APPPATH . 'libraries/fbapi/facebook.php';

class Fb {

    public $ci = null;
    public $client = null;
    public $user_id = null;
    public $user = null;
    public $session = null;
    public $req_perms = '';
    
    function __construct() {
	$this->ci = & get_instance();

	$this->ci->load->config('facebook');

	$app_id = $this->ci->config->item('facebook_app_id');
	$api_key = $this->ci->config->item('facebook_api_key');
	$secret_key = $this->ci->config->item('facebook_secret');
	/*
	 * All perms
	 * user_about_me,user_activities,user_birthday,user_education_history,user_events,user_groups,user_hometown,user_interests,user_likes
	   user_location,user_notes,user_online_presence,user_photo_video_tags,user_photos,user_relationships,user_relationship_details,
	   user_religion_politics,user_status,user_videos,user_website,user_work_history,email,read_friendlists,read_insights,user_checkins,
	   read_mailbox,read_requests,read_stream,xmpp_login,ads_management,user_checkins,publish_stream,create_event,rsvp_event,sms,offline_access,
	   publish_checkins,manage_pages
	 *			
	 */
	
	$this->req_perms = 'manage_pages,user_photo_video_tags,user_photos,user_videos,email,user_about_me';
	
	$this->client = new Facebook(array(
	    'appId' => $app_id,
	    'secret' => $secret_key,
	    'req_perms'=>$this->req_perms,
	    'cookie' => true,
	));
	
	$this->session = $session = $this->client->getSession();
	//Codeigniter Session & facebook cookies don't get along well
	if ($session){
	    try {
		$this->user_id = $this->client->getUser();
	    } catch (FacebookApiException $e ){
		$cookie_name = 'fbs_'. $app_id;
		$dom = '.'.$_SERVER['HTTP_HOST'];
		if (array_key_exists($cookie_name, $_COOKIE)){
		    setcookie($cookie_name, null, time()-4200, '/', $dom);
		    unset($_COOKIE[$cookie_name]);
		}
		
		if (array_key_exists($cookie_name, $_REQUEST)){
		    unset($_REQUEST[$cookie_name]);
		}
		
		$cookies = array($api_key.'_expires', $api_key.'_session_key', $api_key.'_ss', $api_key.'_user', $api_key, 'base_domain_'.$api_key);
		foreach ($cookies as $var){
		    if (array_key_exists($var, $_COOKIE)){
			setcookie($var, null, time()-4200, '/', $dom);
			unset($_COOKIE[$var]);
		    }
		    
		    if (array_key_exists($var, $_REQUEST)){
			unset($_REQUEST[$var]);
		    }
		}
	    }
	}
	
    }
    
    function is_connected(){
	if ($this->client->getSession()) return true;
	return false;
    }
    
    function login_url($next = null){
	if ($next == null) $next = current_url();
	return $this->client->getLoginUrl(array('next'=>$next, 'display'=>'popup', 'req_perms'=>$this->req_perms));
    }
    
    function logout_url($return = null){
	if ($return == null) $return = current_url;
	return $this->client->getLogoutUrl(array('next'=>$return));
    }
    
    function image_url($uid = null, $large = true){
	if ($uid == null) $uid = $this->user_id;
	return 'http://graph.facebook.com/'.$uid.'/picture' . ($large == true ? '?type=large' : '' );
    }
    
    function pages(){
	return $this->api('/me/accounts');
    }
}