<?php
//determine ip of user
$ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR );

require_once('includes/functions_file.inc.php');
require_once('includes/functions_date.inc.php');
require_once('includes/functions_afb.inc.php');
require_once('includes/magic_quotes.php');


//get afb settings from db
$sql = "SELECT * FROM `".TABLE_PREFIX."settings`";
$result = $db->Execute($sql);
$settings = array();
while (!$result->EOF){
  $settings[stripslashes($result->fields['setting_name'])] = stripslashes($result->fields['setting_value']);
  $result->MoveNext();
}

//---------------------------------------------------------------------------------------------
// TIME ZONES
//our offset is 36000 seconds from UTC.
//so the aim is to get the server offset, compare it with ours, and create the current time..
//this should work for QLD'ers even tho we don't have daylight savings
$thisoffset = date("Z");
if (!isset($settings['timezone'])) {
	$settings['timezone'] = $thisoffset;
}
$difference = $settings['timezone'] - $thisoffset;
define('OURTIME',time() + $difference);
define('TZ_OFFSET',$settings['timezone']);
//Get the BASE GMT 0 Timestamp
define('GMT_TIME',time() - $thisoffset);
//--------------------------------------------------------------------------------------------

require_once('includes/class.afbaccount.php');
require_once('includes/auth.inc.php');


//share perms
$share_perms = array();

//shares
$shares = afbGetShares();

//links
$links = array();
$sql = "SELECT * FROM `".TABLE_PREFIX."links` ORDER BY `link_order`,`link_name`";
$result = $db->Execute($sql);
while (!$result->EOF) {
	$links[] = array(
		'id'=>$result->fields['link_id'],
		'name'=>htmlspecialchars(stripslashes($result->fields['link_name'])),
		'href'=>$_SERVER['PHP_SELF'].'?clickthru='.$result->fields['link_id'],
		'target'=>stripslashes($result->fields['link_target'])
	);
	$result->MoveNext();
}

//blackists dirs
$blacklist_dirs = array();
$sql = "SELECT DISTINCT `blacklist_dir` FROM `".TABLE_PREFIX."blacklist_dirs`";
$result = $db->Execute($sql);
while (!$result->EOF) {
	$blacklist_dirs[] = stripslashes($result->fields['blacklist_dir']);
	$result->MoveNext();
}

//blacklist files
$blacklist_files = array();
$sql = "SELECT DISTINCT `blacklist_file` FROM `".TABLE_PREFIX."blacklist_files`";
$result = $db->Execute($sql);
while (!$result->EOF) {
	$blacklist_files[] = stripslashes($result->fields['blacklist_file']);
	$result->MoveNext();
}

//blacklist types
$blacklist_types = array();
$sql = "SELECT DISTINCT `blacklist_extension` FROM `".TABLE_PREFIX."blacklist_ext`";
$result = $db->Execute($sql);
while (!$result->EOF) {
	$blacklist_types[] = stripslashes($result->fields['blacklist_extension']);
	$result->MoveNext();
}



?>