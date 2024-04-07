<?php
//check if link exists
$sql = "SELECT `link_id`,`link_href` FROM `".TABLE_PREFIX."links` WHERE `link_id`='".$_REQUEST['clickthru']."' LIMIT 0,1";
$result = $db->Execute($sql);
while (!$result->EOF){
	//log it
	afbLogit('link',$result->fields['link_id']);
	header("Location: ".stripslashes($result->fields['link_href']));
	exit();
	$result->MoveNext();
}
?>