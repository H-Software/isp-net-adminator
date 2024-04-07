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

require_once('includes/config.inc.php');
require_once('includes/settings.inc.php');

$mode = strtolower(trim($_REQUEST['mode']));
$section = strtolower(trim($_REQUEST['section']));
$action = strtolower(trim($_REQUEST['action']));
$mainmenu = array();


//Check if is an admin
if ($afbAccount->account['account_admin'] != 'yes') {
	//LOGIN
	
	header("Location: http://".$_SERVER['HTTP_HOST'].BASEURL.'?login');
	exit();	

	//END LOGIN
	
} else {
	//Authenticated
	
	
	//create main menu items
	
	//Home
	$selected = false;
	if (strlen($mode) == 0) {
		$selected = true;
	}
	$mainmenu[] = array(
		'name'=>'Home',
		'link'=>$_SERVER['PHP_SELF'],
		'title'=>'Administration Home',
		'selected'=>$selected
	);
	//Shares
	$selected = false;
	if ($mode == 'shares') {
		$selected = true;
	}
	$mainmenu[] = array(
		'name'=>'Shares',
		'link'=>$_SERVER['PHP_SELF'].'?mode=shares',
		'title'=>'Manage Shares',
		'selected'=>$selected
	);
	//Blacklists
	$selected = false;
	if ($mode == 'blacklists') {
		$selected = true;
	}
	$mainmenu[] = array(
		'name'=>'Blacklists',
		'link'=>$_SERVER['PHP_SELF'].'?mode=blacklists',
		'title'=>'Block certain directories, files and types.',
		'selected'=>$selected
	);
	//Accounts
	$selected = false;
	if ($mode == 'accounts') {
		$selected = true;
	}
	$mainmenu[] = array(
		'name'=>'Accounts',
		'link'=>$_SERVER['PHP_SELF'].'?mode=accounts',
		'title'=>'Manage users and administrators',
		'selected'=>$selected
	);
	//Links
	$selected = false;
	if ($mode == 'links') {
		$selected = true;
	}
	$mainmenu[] = array(
		'name'=>'Links',
		'link'=>$_SERVER['PHP_SELF'].'?mode=links',
		'title'=>'Manage links on your site',
		'selected'=>$selected
	);
	//Log
	$selected = false;
	if ($mode == 'reports') {
		$selected = true;
	}
	$mainmenu[] = array(
		'name'=>'Reports',
		'link'=>$_SERVER['PHP_SELF'].'?mode=reports',
		'title'=>'View Reports',
		'selected'=>$selected
	);
	//Settings
	$selected = false;
	if ($mode == 'settings') {
		$selected = true;
	}
	$mainmenu[] = array(
		'name'=>'Settings',
		'link'=>$_SERVER['PHP_SELF'].'?mode=settings',
		'title'=>'Edit Site Settings',
		'selected'=>$selected
	);
	//logout
	$mainmenu[] = array(
		'name'=>'Logout',
		'link'=>$_SERVER['PHP_SELF'].'?logout',
		'title'=>'Logout',
		'selected'=>false
	);
	
	//header
	include('admin/head.php');
	
	//determine which section we're dealing with
	switch ($mode) {
		case 'shares':
			include('admin/shares.inc.php');
			break;
			
		case 'blacklists':
			include('admin/blacklists.inc.php');
			break;
		
		case 'accounts':
			include('admin/accounts.inc.php');
			break;
			
		case 'links':
			include('admin/links.inc.php');
			break;
		
		case 'reports':
			include('admin/reports.inc.php');
			break;
		
		case 'settings':
			include('admin/settings.inc.php');
			break;
			
		default:
			//home
			include('admin/home.inc.php');
			
			break;
	} //end switch

	
	
	
	//END Authenticated
}

//footer
include('admin/footer.php');
?>