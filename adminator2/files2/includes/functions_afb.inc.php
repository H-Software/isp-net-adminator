<?php /*
=====================================================================

  functions_afb.inc.php
	
	Functions specific to this application.

===================================================================== */



//------------------------------------------------------------------------
//afbGetShares
function afbGetShares() {
	//this function returns the shares available to the user
	global $db;
	global $share_perms;
	
	//setup shares
	$shares = array();
	
	$sql = "SELECT * FROM `".TABLE_PREFIX."shares` ORDER BY `share_name`";
	$result = $db->Execute($sql);
	while (!$result->EOF){
		//get perms based on group and account accumulation
		$perms = afbGetSharePerms($result->fields['share_id']);
		//set share perms for this share
		$share_perms[$result->fields['share_id']] = $perms;
		//check if we're displaying it
		if ($perms['r'] || $perms['ul']) {
			//read or upload ability
			$shares[] = array(
				'id'=>$result->fields['share_id'],
				'name'=>htmlspecialchars(stripslashes($result->fields['share_name'])),
				'description'=>htmlspecialchars(stripslashes($result->fields['share_description'])),
				'dir'=>stripslashes($result->fields['share_dir'])
			);
		}
		//next share please
		$result->MoveNext();
	}
	//return some love.
	return $shares;
	
} // end afbGetShares function

//------------------------------------------------------------------------
//afbGetSharePerms
function afbGetSharePerms($share_id) {
	global $afbAccount;
	global $db;
	
	if ($afbAccount) {
		$account_id = $afbAccount->account['account_id'];
	} else {
		$account_id = 0;
	}
	
	//set default perms
	$perms = array('r'=>false,'dl'=>false,'ul'=>false,'del'=>false);
	
	if ($afbAccount->account['account_admin'] == 'yes') {
		$perms = array('r'=>true,'dl'=>true,'ul'=>true,'del'=>true);
		return $perms;
	}
	
	
	//Get permissions from groups
	$groups = array();
	$sql = "SELECT `group_id` FROM `".TABLE_PREFIX."accounts_has_groups` WHERE `account_id`='".$account_id."'";
	$result = $db->Execute($sql);
	while (!$result->EOF){
		$groups[] = $result->fields['group_id'];
		$result->MoveNext();
	}
	
	for ($x=0;$x<count($groups);$x++) {
		$sql = "SELECT * FROM `".TABLE_PREFIX."groups_has_perms` WHERE `group_id`='".$groups[$x]."' AND `share_id`='".$share_id."'";
		$result = $db->Execute($sql);
		$recordcount = $result->RecordCount();
		if ($recordcount > 0) {
			if ($result->fields['perm_r'] == 'yes') {
				$perms['r'] = true;
			}
			if ($result->fields['perm_dl'] == 'yes') {
				$perms['dl'] = true;
			}
			if ($result->fields['perm_ul'] == 'yes') {
				$perms['ul'] = true;
			}
			if ($result->fields['perm_del'] == 'yes') {
				$perms['del'] = true;
			}
		}
	}
	
	//get perms based on account
	$sql = "SELECT * FROM `".TABLE_PREFIX."accounts_has_perms` WHERE `account_id`='".$account_id."' AND `share_id`='".$share_id."'";
	$result = $db->Execute($sql);
	$recordcount = $result->RecordCount();
	if ($recordcount > 0) {
		if ($result->fields['perm_r'] == 'yes') {
			$perms['r'] = true;
		}
		if ($result->fields['perm_dl'] == 'yes') {
			$perms['dl'] = true;
		}
		if ($result->fields['perm_ul'] == 'yes') {
			$perms['ul'] = true;
		}
		if ($result->fields['perm_del'] == 'yes') {
			$perms['del'] = true;
		}
	}
	
	return $perms;
	
} //end afbGetSharePerms function

//------------------------------------------------------------------------
//afbLogit
function afbLogit($action,$data='',$share_id=0) {
	global $db;
	global $afbAccount;
	global $ip;
	
	$account_id = 0;
	if ($afbAccount) {
		$account_id = $afbAccount->account['account_id'];
	}
	
	$sql = "INSERT INTO `".TABLE_PREFIX."log` (
		`log_action`,
		`log_data`,
		`account_id`,
		`share_id`,
		`log_timestamp`,
		`log_ip`
	) VALUES (
		'".addslashes($action)."',
		'".addslashes($data)."',
		'".$account_id."',
		'".$share_id."',
		'".GMT_TIME."',
		'".$ip."'
	)";
	if ($db->Execute($sql) === false) { 
		//echo 'Error: '.$db->ErrorMsg();
		return false;
	}
	return true;
} //end afbLogit function

//------------------------------------------------------------------------
//CheckFolder
function CheckFolder($share_root='/',$path='/') {
	global $blacklist_dirs;
	if (is_dir($path)) {
		//get all directory names
		$dirs = explode('/',str_replace($share_root,'',$path));
		for ($x=0;$x<count($dirs);$x++) {
			//check blacklist
			if (@in_array(strtolower($dirs[$x]),$blacklist_dirs)) {
				return false;
			}
		}
		return true;
	} else {
		return false;
	}
} //end function

//------------------------------------------------------------------------
//CheckFile
function CheckFile($path) {
	global $blacklist_types;
	global $blacklist_files;
	
	if (is_file($path)) {
		//Get filename
		$file = strtolower(GetFilename($path));
		//check against blacklist				
		if (@in_array(strtolower($file),$blacklist_files)) {
			return false;
		}
		
		//Get extension
		$ext = GetExt($file);
		if (@in_array(strtolower($ext),$blacklist_types)) {
			return false;
		}
		
		return true;
	} else {
		return false;
	}
} //end function



?>