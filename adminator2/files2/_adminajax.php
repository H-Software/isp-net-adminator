<?php
//simulate a remote connection
//sleep(1);

// ------------------------------------------------------------------------------------------------
// INCLUDES
// This sets the include path relative to the current directory.
session_start();

//check for config file.
if (!file_exists('includes/config.inc.php')) {
	//send to install
	header("Content-type: text/xml");
	echo chr(60).chr(63).'xml version="1.0" encoding="utf-8" '.chr(63).chr(62);
	echo '<xmlresponse>';
	SetError('1000','Application has not been installed yet. Run install.php');
	echo '</xmlresponse>';
	exit();
}
// now that all is well, include config files.
require_once('includes/config.inc.php');
require_once('includes/settings.inc.php');
// ------------------------------------------------------------------------------------------------
header("Content-type: text/xml; charset=utf-8");
echo chr(60).chr(63).'xml version="1.0" encoding="utf-8" '.chr(63).chr(62);
?>
<xmlresponse>
  <?php
	if ($afbAccount->account['account_admin'] == 'yes') {
		
		switch ($_REQUEST['mode']) {
			
			case 'blacklist_dirs':
				// &edit
				// &value
				if (isset($_REQUEST['edit'])) {
					$sql = "UPDATE `".TABLE_PREFIX."blacklist_dirs` SET `blacklist_dir`='" . $_REQUEST['value'] . "' WHERE `blacklist_id`='" . $_REQUEST['edit'] . "'";
					if ($db->Execute($sql) === false) { 
						SetError(41,'Error: '.$db->ErrorMsg());
					} else {
						SetError();
					}
				} else {
					SetError(10,'What are you looking for?');
				}
				break;
				
			case 'blacklist_files':
				// &edit
				// &value
				if (isset($_REQUEST['edit'])) {
					$sql = "UPDATE `".TABLE_PREFIX."blacklist_files` SET `blacklist_file`='" . $_REQUEST['value'] . "' WHERE `blacklist_id`='" . $_REQUEST['edit'] . "'";
					if ($db->Execute($sql) === false) { 
						SetError(41,'Error: '.$db->ErrorMsg());
					} else {
						SetError();
					}
				} else {
					SetError(11,'Do you get paid for this?');
				}
				break;
				
			case 'blacklist_types':
				// &edit
				// &value
				if (isset($_REQUEST['edit'])) {
					$sql = "UPDATE `".TABLE_PREFIX."blacklist_ext` SET `blacklist_extension`='" . $_REQUEST['value'] . "' WHERE `blacklist_id`='" . $_REQUEST['edit'] . "'";
					if ($db->Execute($sql) === false) { 
						SetError(41,'Error: '.$db->ErrorMsg());
					} else {
						SetError();
					}
				} else {
					SetError(12,'You know, I used to be like you. Then I got a life.');
				}
				break;
				
			case 'account_groups':
				// &edit
				// &value
				if (isset($_REQUEST['edit'])) {
					$sql = "UPDATE `".TABLE_PREFIX."groups` SET `group_name`='" . $_REQUEST['value'] . "' WHERE `group_id`='" . $_REQUEST['edit'] . "'";
					if ($db->Execute($sql) === false) { 
						SetError(41,'Error: '.$db->ErrorMsg());
					} else {
						SetError();
					}
				} else {
					SetError(13,'My mama could hack better than that.');
				}
				break;
				
			default:
				SetError(200,'Naughty naughty! Stop hacking! Do your homework.');
				break;
		
		}
		
		
	} else {
		SetError(100,'Not logged in as an administrator!');
	}
	?> 
</xmlresponse><?php
// ----------------------------------------------------------------------------------------------------
// FUNCTIONS 
// ----------------------------------------------------------------------------------------------------

//this sets the error code node - essential for every xml document to be returned.
function SetError($code=0,$text='') {
	echo "<error_code>" . $code . "</error_code>\n<error>" . htmlspecialchars($text) . "</error>";
}
?>