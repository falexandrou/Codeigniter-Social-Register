<?php
/******
* Codeigniter Social Register/Auth Sub system
* @author Fotis Alexandrou - fotis@redmark.gr
* @version 0.1
* @license Free/Open source
* http://www.redmark.gr
* Please read README.txt first
******/
class User extends CI_Model {
    /**
     * The user's id
     * 
     * @var type integer/string that represents a number
     */
    public $id = 0;
    
    /**
     * The user's facebook user id
     * 
     * @var type integer/string that represents a number
     */
    public $facebook_id = 0;
    
    /**
     * The user's twitter user id
     * @var type integer/string that represents a number
     */
    public $twitter_id = 0;
    
    /**
     * Access the parent methods
     */
    function __construct() {
	parent::__construct();
    }
    
    /**
     * set_id sets the user id in the user's model
     * The id could be either an integer or a string that represents a number (is_numeric() == true)
     * 
     * @param type $id 
     */
    public function set_id($id) {
	$this->id = $id;
    }
    
    /**
     * Set the facebook id for the current user
     * @param type $id 
     */
    public function set_facebook_id($id) {
	$this->facebook_id = $id;
    }
    
    /**
     * Set the twitter id for the user
     * @param type $id 
     */
    public function set_twitter_id($id) {
	$this->twitter_id = $id;
    }
    
    /**
     * Returns a user object for the given user_id
     * 
     * @return type User object
     */
    public function get() {
	if ( !is_numeric($this->id) || (int)$this->id <= 0) return;
	$sql = "SELECT 
		`u`.`id`, `u`.`real_name`, `u`.`date_added`, `u`.`login_type`, `u`.`active`,
		`uf`.`email`, `uf`.`facebook_user_id`, 
		`uf`.`bio` AS `fb_bio`, `uf`.`handle` AS `fb_handle`,
		`uf`.`profile_image_url` AS `fb_profile_image_url`,
		`ut`.`twitter_user_id`, `ut`.`handle` AS `tw_handle`, `ut`.`bio` AS `tw_bio`,
		`ut`.`profile_image_url` AS `tw_profile_image_url`
		FROM `users` `u`
		LEFT JOIN `users_facebook` `uf` ON (`uf`.`user_id`=`u`.`id`)
		LEFT JOIN `users_twitter` `ut` ON (`ut`.`user_id`=`u`.`id`)
		WHERE `u`.`id`={$this->id} AND `u`.`active`=1
		LIMIT 1";
	$res = $this->db->query($sql)->result();
	
	if (empty($res) || empty($res[0]) || !isset($res[0]->id)) return;
	
	$user = $res[0];

	$user->profile_url = $user->image = $user->bio = $user->handle = null;
	
	if ($user->login_type == 'facebook'){
	    $user->image = $user->fb_profile_image_url;
	    $user->bio = $user->fb_bio;
	    $user->handle = $user->fb_handle;
	    if ($user->handle!=null){
		$user->profile_url = 'http://facebook.com/'.$user->handle;
	    }
	}else if ($user->login_type == 'twitter'){
	    $user->image = $user->tw_profile_image_url;
	    $user->bio = $user->tw_bio;
	    $user->handle = $user->tw_handle;
	    if ($user->handle!=null){
		$user->profile_url = 'http://twitter.com/'.$user->handle;
	    }
	}
	
	return $user;
    }
    
    /**
     * Get a user by his/her facebook id
     * @return type User object
     */
    public function get_by_facebook() {
	if ( !is_numeric($this->facebook_id) || (int)$this->facebook_id <= 0) return;
	
	$sql = "SELECT `u`.`id`, `u`.`real_name`, `u`.`active`, `u`.`date_added`, `u`.`login_type`
		FROM `users_facebook` `uf`
		LEFT JOIN `users` `u` ON (`u`.`id`=`uf`.`user_id`)
		WHERE `u`.`active`=1 AND `uf`.`facebook_user_id`={$this->facebook_id}
		LIMIT 1";
	
	$res = $this->db->query($sql)->result();
	
	if (empty($res) || empty($res[0]) || !isset($res[0]->id)) return;
	
	return $res[0];
    }
    
    /**
     * Get a user by his/her twitter id
     * @return type User object
     */
    public function get_by_twitter() {
	if ( !is_numeric($this->twitter_id) || (int)$this->twitter_id <= 0) return;
	
	$sql = "SELECT `u`.`id`, `u`.`real_name`, `u`.`active`, `u`.`date_added`, `u`.`login_type`
		FROM `users_twitter` `ut`
		LEFT JOIN `users` `u` ON (`u`.`id`=`ut`.`user_id`)
		WHERE `u`.`active`=1 AND `ut`.`twitter_user_id`={$this->twitter_id}
		LIMIT 1";
	
	$res = $this->db->query($sql)->result();
	
	if (empty($res) || empty($res[0]) || !isset($res[0]->id)) return;
	
	return $res[0];
    }
    
    /**
     * Function for administrative purposes 
     * Returns users for a given range
     * 
     * @param type $offset
     * @param type $limit
     * @return type array of user objects
     */
    public function get_all($offset=0, $limit = 0) {
	$sql = "SELECT `id`, `real_name`, `active`, `date_added`, `login_type`
		FROM `users`";
	
	if ((int)$limit > 0){
	    $sql .= " LIMIT $offset, $limit";
	}
	
	return $this->db->query($sql)->result();
    }
    
    
    /**
     * Returns the user's login type
     * @return type string
     */
    public function get_login_type() {
	if ( !is_numeric($this->id) || (int)$this->id <= 0) return;
	$sql = "SELECT `login_type` FROM `users` WHERE `id`={$this->id} AND `active`=1 LIMIT 1";
	$res = $this->db->query($sql)->result();
	
	if (empty($res) || empty($res[0]) || !isset($res[0]->login_type) || $res[0]->login_type == null) return;
	
	return $res[0]->login_type;
    }
    
    /**
     * Bans a user. Prevents from logging in
     * @return type boolean
     */
    public function ban_user() {
	if ( !is_numeric($this->id) || (int)$this->id <= 0) return false;
	$sql = "UPDATE `users` SET `active`=0 WHERE `id`={$this->id} LIMIT 1";
	$this->db->query($sql);
	return true;
    }
    
    /**
     *
     * @param type $real_name
     * @param type $login_type
     * @return type int
     */
    public function add($real_name, $login_type=null) {
	if ( $real_name == null || $login_type == null || !in_array($login_type, array('facebook', 'twitter')) ){
	    return false;
	}
	
	$sql = "INSERT INTO `users`
		(`real_name`, `active`, `date_added`, `login_type`)
		VALUES
		('$real_name', 1, NOW(), '$login_type')";
	
	$this->db->query($sql);
	$this->id = $this->db->insert_id();
	return $this->id;
    }
    
    /** 
     * Stores a user's facebook information
     *
     * @param type $fb_id
     * @param type $email
     * @param type $profile_url
     * @return type boolean
     */
    public function store_facebook($fb_id, $email, $profile_image_url, $bio) {
	if ( !is_numeric($this->id) || (int)$this->id <= 0) return false;
	
	$sql = "INSERT INTO `users_facebook`
		(`user_id`, `email`, `facebook_user_id`, `profile_image_url`, `bio`)
		VALUES
		({$this->id}, '{$email}', '{$fb_id}', '{$profile_image_url}', '{$bio}')
		ON DUPLICATE KEY UPDATE `email` = VALUES(`email`),
		`profile_image_url`=VALUES(`profile_image_url`), `bio`=VALUES(`bio`)";
	
	$this->db->query($sql);
	return true;
    }
    
    /**
     * Stores a user's twitter information
     * 
     * @param type $tw_id
     * @param type $handle
     * @param type $real_name
     * @param type $bio
     * @param type $profile_image_url
     * @return type boolean
     */
    public function store_twitter($tw_id, $handle, $real_name, $bio, $profile_image_url, $handle) {
	if ( !is_numeric($this->id) || (int)$this->id <= 0) return false;
	$sql = "INSERT INTO `users_twitter`
		(`user_id`, `twitter_user_id`, `handle`, `real_name`, `bio`, `profile_image_url`, `handle`)
		VALUES
		({$this->id}, '{$tw_id}', '$handle', '$real_name', '$bio', '$profile_image_url', '$handle')
		ON DUPLICATE KEY UPDATE `handle`=VALUES(`handle`), `real_name`=VALUES(`real_name`),
		`bio`=VALUES(`bio`), `profile_image_url`=VALUES(`profile_image_url`), `handle`=VALUES(`handle`)";
		
	$this->db->query($sql);
	return true;
    }
}