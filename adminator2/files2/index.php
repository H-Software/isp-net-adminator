<?php

session_start();

//check for config file.
if (!file_exists('includes/config.inc.php')) {
	//send to install
	if (file_exists('install.php')) {
		include('install.php');
		exit();
	} else {
		echo '<pre style="color:#f00;text-align:center;">'."\n".'<strong>There is an error with the application!</strong>'."\n\n".'Please delete all files and replace them with the latest version from <a href="http://blog.jc21.com">jc21.com</a>';
		exit();
	}
}

// now that all is well, include config files.
require_once('includes/config.inc.php');
require_once('includes/settings.inc.php');

//check if clickthru
if (isset($_REQUEST['clickthru']) && strlen($_REQUEST['clickthru']) > 0) {
	include('includes/clickthru.inc.php');
} //end clickthru


//check for download
if (strlen($_REQUEST['share']) > 0) {
	include('includes/download.inc.php');
}


//header
include('includes/head.php');

//check if we have to login


if (count($shares) == 0 || isset($_GET['login'])) {
	//include theme login page

	if (file_exists('themes/'.$theme_dir.'/login.php')) {
		include('themes/'.$theme_dir.'/login.php');
	} else {
		echo '<p>Error loading <strong>themes/'.$theme_dir.'/login.php</strong>, file may not exist.</p>';
		exit();
		}

	

} elseif (isset($_GET['upload'])) {
	//upload files.
	include('includes/upload.inc.php');
	
} else {
	//root.
	//include theme body
	if (file_exists('themes/'.$theme_dir.'/body.php')) {
		include('themes/'.$theme_dir.'/body.php');
	} else {
		echo '<p>Error loading <strong>themes/'.$theme_dir.'/body.php</strong>, file may not exist.</p>';
		exit();
	}
} //end error check

//footer
include('includes/footer.php');
?>