<?php

class Auth {
	private $_siteKey;
//	protected $_cookieName;
	protected $_level;
	protected $_activeTime;
	
	public function __construct(){
		$this->_siteKey = 'FTq7qEv2k^[kZ*b4pGpKm*ht6TC2s#WfBH+dbBL#RxW_^!LdXb';
//		$this->_cookieName = 'NPSATS';
		$this->_activeTime = 86400;
	}
	
//	public function cookieName(){
//		return $this->_cookieName;
//	}
	
	public function level(){
		return $this->_level;
	}

	private function randomString($length = 50){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_-+=[]{}<>?';
		$string = '';
	
		for ($p=0; $p<$length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters)-1)];
		}
	
		return $string;
	}

	protected function hashData($data){
		return hash_hmac('sha512', $data, $this->_siteKey);
	}
	
	private function generateToken(){
		return $this->hashData($this->randomString());
	}
//	public function isAdmin(){
//		//$selection being the array of the row returned from the database.
//		if($selection['is_admin'] == 1) {
//			return true;
//		}
//	
//		return false;
//	}
	
	public function createUser($user, $password, $lev = 3){
		global $mysqli;
		//Generate user's salt
		$user_salt = $this->randomString();
			
		//Salt and Hash the password
		$password = $user_salt . $password;
		$password = $this->hashData($password);
			
		//Create verification code
//		$code = $this->randomString();
	
		//Commit values to database here.
		$query = "INSERT INTO users (user, pass, user_salt, level)
							VALUES (?, ?, ?, ?)";
		$paramTypes = 'sssi';
		$params = array($user, $password,$user_salt, $lev);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
		if($result != false){
			return true;
		}
		return false;
	}
	
	public function login($user, $password)	{
		global $mysqli;
		//Select users row from database based on $user
		$query = "SELECT * FROM users WHERE user=?";
		$paramTypes = 's';
		$params = array($user);
		$selection = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);

		if(!empty($selection)){// we have this user in system
		
			//Salt and hash password for checking
			$cryptpass = $selection[0]['user_salt'] . $password;
			$cryptpass = $this->hashData($cryptpass);
		
			$salt1 = "&&L#@";
			$salt2 = "^b{*!";
			// encrypt the password
			$oldcryptpass = md5($salt1.$password.$salt2);
		
			// if they do not have a user_salt, create one and add them to the new system
			if ($oldcryptpass == $selection[0]['pass'] && '' == $selection[0]['user_salt']){
				// have this user, and password is correct, but they need to be put into
				// new system
				$user_salt = $this->randomString();
				
				//Salt and Hash the password
				$cryptpass = $user_salt . $password;
				$cryptpass = $this->hashData($cryptpass);
				
				//Commit values to database here.
				$query = "UPDATE users SET user_salt='$user_salt', pass='$cryptpass'
									WHERE user=?";
				$paramTypes = 's';
				$params = array($user);
				$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
				
				if('' != $result){
					$selection[0]['pass'] = $cryptpass;
				}
			}
		
			//Check user and password hash match database row
			if($cryptpass == $selection[0]['pass']){
			//Convert to boolean
//		$is_active = (boolean) $selection['is_active'];
//		$verified = (boolean) $selection['verified'];
	
//		if($match == true) {
//			if($is_active == true) {
//				if($verified == true) {
					//Email/Password combination exists, set sessions
					//First, generate a random string.
					$token = Auth::generateToken();
						
					//Setup sessions vars
					$sessId = session_id();
					$remoteAddress = $_SERVER['REMOTE_ADDR'];
					$userAgent = $_SERVER['HTTP_USER_AGENT'];
					$_SESSION['token'] = $token;
					$_SESSION['uid'] = $selection[0]['id'];
					$_SESSION['level'] = $this->_level = $selection[0]['level'];
					$_SESSION['user'] = $selection[0]['user'];
					
					//Delete old logged_in_member records for user
					$query = "DELETE FROM logged_in_users WHERE user_id={$selection[0]['id']}";
					$paramTypes = '';
					$params = array();
					$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
					//Insert new logged_in_member record for user
					$query = "INSERT INTO logged_in_users (user_id, session_id, token, remote_address, user_agent)
												   VALUES ('{$selection[0]['id']}', '$sessId', '$token', '$remoteAddress', '$userAgent')";
					$paramTypes = '';
					$params = array();
					$inserted = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
					setcookie('PHPSESSID',$sessId,time() + $this->_activeTime,'/');
						
					//Logged in
					if($inserted != false) {
						return 0;
					}
					// database failure
					return 3;
				} else {// $verified
					//Not verified bad password
					return 1;
				}
				
//			} else {// $is_active
//				//Not active
//				return 2;
//			}
//		}// $match
		}
		//No match, reject bad user name
		return 4;
	}
	
	public function checkSession(){
		if(isset($_SESSION['uid'])){
			global $mysqli;
			//Select the row
			$query = "SELECT * FROM logged_in_users WHERE user_id=?";
			$paramTypes = 's';
			$params = array($_SESSION['uid']);
			$selection = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);	
		
			if($selection) {
				//Check ID and Token
				if( session_id() == $selection[0]['session_id'] && 
					$_SESSION['token'] == $selection[0]['token'] &&
					$_SERVER['REMOTE_ADDR'] == $selection[0]['remote_address'] && 
					$_SERVER['HTTP_USER_AGENT'] == $selection[0]['user_agent'] ) {
					//Id and token match, refresh the session for the next request
					$this->refreshSession();
					return true;
				}
			}
		}
		return false;
	}
	
	private function refreshSession(){
		global $mysqli;
		//Regenerate id
		session_regenerate_id(true);
	
		//Regenerate token
		$token = Auth::generateToken();
		
		//Store in session
		$sessId = session_id();
		$remoteAddress = $_SERVER['REMOTE_ADDR'];
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['token'] = $token;
		
		// reset auth:level value
		$this->_level = $_SESSION['level'];
		
		//Insert new logged_in_member record for user
		$query = "UPDATE logged_in_users SET session_id='$sessId', token='$token', remote_address='$remoteAddress', user_agent='$userAgent'
								WHERE user_id='{$_SESSION['uid']}'";
		$paramTypes = '';
		$params = array();
		$inserted = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		setcookie('PHPSESSID',$sessId,time() + $this->_activeTime,'/');
	}
	
	public function logout(){
		if(isset($_SESSION['uid'])){
			global $mysqli;
			//Delete old logged_in_member records for user
			$query = "DELETE FROM logged_in_users WHERE user_id={$_SESSION['uid']}";
			$paramTypes = '';
			$params = array();
			$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
		setcookie('PHPSESSID','', time()-1);
		$_SESSION = array();
		session_destroy();
		reDirectHome('login.php');
	}
}
?>