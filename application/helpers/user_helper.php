<?php
/******
* Codeigniter Social Register/Auth Sub system
* @author Fotis Alexandrou - fotis@redmark.gr
* @version 0.1
* @license Free/Open source
* http://www.redmark.gr
* Please read README.txt first
******/

/**
 * Returns a user's login status 
 * 
 * @return type boolean
 */
function is_logged() {
    $ci = & get_instance();
    $user_id = $ci->session->userdata('user_id');
    $login_type = $ci->session->userdata('login_type');
    /**
     * TODO: Make things more secure here
     */
    $user = null;
    if (is_numeric($user_id)){
	$ci->load->model('user');
	$ci->user->set_id($user_id);
	$user = $ci->user->get();
    }
    
    if ($user_id == null || $user == null)
	return false;
    
    if ($login_type == 'facebook'){
	$ci->load->library('fb');
	return $ci->fb->is_connected();
    }else if ($login_type == 'twitter'){
	$ci->load->library('tweet');
	return $ci->tweet->logged_in();
    }
    return false;
}

function get_user_id(){
    if (!is_logged()) return false;
    $ci = & get_instance();
    return $ci->session->userdata('user_id');
}

function get_user(){
    if (!is_logged()) return false;
    $ci = & get_instance();
    $user_id = $ci->session->userdata('user_id');
    //REMOVE IF AUTOLOADED
    $ci->load->model('user');
    $ci->user->set_id($user_id);
    return $ci->user->get();
}