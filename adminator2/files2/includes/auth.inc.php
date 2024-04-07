<?php
/*
  Authentication for Accounts.
	
	Checks their session details against the database.
	If not authenticated, returns them to /index.php
	with an error.
		
	@author Jamie Curnow



*/

//set default theme_dir
$theme_dir = 'default';

//clear the $afbAccount class, if it's already set for any reason
if (isset($afbAccount)) {
	unset($afbAccount);
}

//check if login form was posted, and assign those vars to the session
if (isset($_POST['afb_username']) && isset($_POST['afb_password'])) {
	$_SESSION['afb_username'] = trim(strtolower(stripslashes($_POST['afb_username'])));
	$_SESSION['afb_password'] = stripslashes($_POST['afb_password']);
}

//check cookies
if (!isset($_SESSION['afb_username']) && !isset($_SESSION['afb_password'])) {
	if (isset($_COOKIE['afb_un']) && isset($_COOKIE['afb_pw'])) {
		$_SESSION['afb_username'] = trim(strtolower(stripslashes($_COOKIE['afb_un'])));
		$_SESSION['afb_password'] = stripslashes($_COOKIE['afb_pw']);
	}
}

//check for the session vars
if (isset($_SESSION['afb_username']) && isset($_SESSION['afb_password'])) {

	//create class and validate
	$afbAccount = new afbAccount($_SESSION['afb_username'],$_SESSION['afb_password']);
	
	if ($afbAccount->validate()) {
		//user is validated!
		//check if user wants to logout
		if (isset($_GET['logout'])) {
			//get the sc_id of the user, if the credentials are even ok. Don't do this, if we logged in via admin.
			//$afbAccount->logUser('logout');
			afbLogit('logout');
			//unset their session details
			unset($_SESSION['afb_username'],$_SESSION['afb_password']);
			unset($afbAccount);
			//delete cookies
			// set the expiration date to one day ago
			setcookie("afb_un", "", time() - (60*60*24));
			setcookie("afb_pw", "", time() - (60*60*24), "/");
			header("Location: http://".$_SERVER['HTTP_HOST'].BASEURL);
			
		} else {
			//user doesn't want to logout
			//check their theme_dir
			$theme_dir = $afbAccount->account['theme'];
			//check if user just logged in		
			if (isset($_POST['afb_username']) && isset($_POST['afb_password'])) {
				//log their action
				afbLogit('login');
				//check if they want their details remembered.
				
				if (abs($_POST['afb_remember_me']) == 1) {
					//set cookie timeout of 2 weeks...
					$timeout = time() + (60 * 60 * 24 * 14);
					if (!setcookie('afb_un',$_POST['afb_username'],$timeout,'/')) {
						//cookie not set!
					}
					if (!setcookie('afb_pw',$_POST['afb_password'],$timeout,'/')) {
						//cookie not set!
					}
				}
				
			} //end form post check

		} //end logout check
		
		
	} else {
		//user is not validated.
		//check if they just logged in, and set an error if they did.
		if (isset($_POST['afb_username']) && isset($_POST['afb_password'])) {
			$login_error = $afbAccount->error;
		}
		
	} //end validated check
	
} //end session vars check


//double check theme_dir
if (!is_dir('themes/'.$theme_dir) || strlen($theme_dir) == 0) {
	$theme_dir = $settings['default_theme'];
	if (!is_dir('themes/'.$theme_dir) || strlen($theme_dir) == 0) {
		$theme_dir = 'afb_blue';
	}
}
define('THEMEDIR',BASEURL.'themes/'.$theme_dir.'/');
?>