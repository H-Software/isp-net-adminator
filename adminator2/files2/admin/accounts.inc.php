<?php
//create account submit
if (isset($_POST['create_submit'])) {
	//look for errors
	
	//name
	if (strlen(trim($_POST['account_name'])) == 0) {
		$errors[] = 'Account Name is required';
	}
	//username
	if (strlen(trim($_POST['account_username'])) == 0) {
		$errors[] = 'Account Username is required';
	} else {
		//check if username is taken
		$sql = "SELECT COUNT(`account_id`) AS `cnt` FROM `".TABLE_PREFIX."accounts` WHERE `account_username`='".trim($_POST['account_username'])."'";
		$result = $db->Execute($sql);
		if ($result->fields['cnt'] > 0) {
			$errors[] = 'Account Username is already taken';
		}
	}
	//password
	if (strlen(trim($_POST['account_password'])) == 0) {
		$errors[] = 'Account Password is required';
	} else {
		if ($_POST['account_password'] !== $_POST['account_password2']) {
			$errors[] = 'Account Passwords do not match';
		}
	}
	
	if (count($errors) == 0) {
		//ok to create
		
		$sql = "INSERT INTO `".TABLE_PREFIX."accounts` (
			`account_name`,
			`account_username`,
			`account_password`,
			`account_disabled`,
			`account_admin`,
			`theme`
		) VALUES (
			'".$_POST['account_name']."',
			'".$_POST['account_username']."',
			'".$_POST['account_password']."',
			'".$_POST['account_disabled']."',
			'".$_POST['account_admin']."',
			'".$_POST['theme']."'
		)";
		if ($db->Execute($sql) === false) { 
			$errors[] = 'Error: '.$db->ErrorMsg();
			$_GET['create'] = true;
		}
	} else {
		//send back to create page
		$_GET['create'] = true;
	}
	
//edit account submit
} elseif (isset($_POST['edit_submit'])) {
	//look for errors
	
	//name
	if (strlen(trim($_POST['account_name'])) == 0) {
		$errors[] = 'Account Name is required';
	}
	//username
	if (strlen(trim($_POST['account_username'])) == 0) {
		$errors[] = 'Account Username is required';
	} else {
		//check if username is taken
		$sql = "SELECT COUNT(`account_id`) AS `cnt` FROM `".TABLE_PREFIX."accounts` WHERE `account_username`='".trim($_POST['account_username'])."' AND `account_id` <> '".$_POST['account_id']."'";
		$result = $db->Execute($sql);
		if ($result->fields['cnt'] > 0) {
			$errors[] = 'Account Username is already taken';
		}
	}
	//password
	if (strlen(trim($_POST['account_password'])) == 0) {
		$errors[] = 'Account Password is required';
	} else {
		if ($_POST['account_password'] !== $_POST['account_password2']) {
			$errors[] = 'Account Passwords do not match';
		}
	}
	
	//check if we're editing ourself
	if ($afbAccount->account['account_id'] == $_POST['account_id']) {
		$_POST['account_admin'] = 'yes';
		$_POST['account_disabled'] = 'no';
	}
	
	if (count($errors) == 0) {
		//ok to update
		
		$sql = "UPDATE `".TABLE_PREFIX."accounts` SET
			`account_name`='".$_POST['account_name']."',
			`account_username`='".$_POST['account_username']."',
			`account_password`='".$_POST['account_password']."',
			`account_disabled`='".$_POST['account_disabled']."',
			`account_admin`='".$_POST['account_admin']."',
			`theme`='".$_POST['theme']."'
		WHERE `account_id`='".$_POST['account_id']."'
		LIMIT 1";
		if ($db->Execute($sql) === false) { 
			$errors[] = 'Error: '.$db->ErrorMsg();
			$_GET['edit'] = $_POST['account_id'];
		} else {
			if ($afbAccount->account['account_id'] == $_POST['account_id']) {
				$_SESSION['afb_username'] = $_POST['account_username'];
				$_SESSION['afb_password'] = $_POST['account_password'];
			}
		}
		
	} else {
		//send back to edit page
		$_GET['edit'] = $_POST['account_id'];
	}
	
//delete account submit
} elseif(isset($_GET['remove'])) {
	$sql = "DELETE FROM `".TABLE_PREFIX."accounts` WHERE `account_id`='" . $_GET['remove'] . "'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	} else {
		if ($db->Affected_Rows()) {
			$sql = "DELETE FROM `".TALBE_PREFIX."accounts_has_groups` WHERE `account_id`='" . $_GET['remove'] . "'";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
			$sql = "DELETE FROM `".TALBE_PREFIX."accounts_has_perms` WHERE `account_id`='" . $_GET['remove'] . "'";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
			$affirmative = 'Account has been removed';
		}
	}
	
//new group submission
} elseif (isset($_POST['new_group'])) {
	if (strlen($_POST['new_group']) > 0) {
		//check if already exists.
		$sql = "SELECT COUNT(`group_id`) AS `cnt` FROM `".TABLE_PREFIX."groups` WHERE `group_name` LIKE '".$_POST['new_group']."'";
		$result = $db->Execute($sql);
		if ($result->fields['cnt'] == 0) {
			//insert
			$sql = "INSERT INTO `".TABLE_PREFIX."groups` (`group_name`) VALUES ('" . $_POST['new_group'] . "')";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
		}		
	}

//delete group submission
} elseif (isset($_GET['remove_group'])) {
	$sql = "DELETE FROM `".TABLE_PREFIX."groups` WHERE `group_id`='".$_GET['remove_group']."'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	} else {
		$sql = "DELETE FROM `".TABLE_PREFIX."accounts_has_groups` WHERE `group_id`='".$_GET['remove_group']."'";
		if ($db->Execute($sql) === false) { 
			$negative = 'Error: '.$db->ErrorMsg();
		}
		$sql = "DELETE FROM `".TABLE_PREFIX."groups_has_perms` WHERE `group_id`='".$_GET['remove_group']."'";
		if ($db->Execute($sql) === false) { 
			$negative = 'Error: '.$db->ErrorMsg();
		}
		$affirmative = 'Group has been removed.';
	}

//add to group submission
} elseif (isset($_POST['add_submit'])) {
	for ($x=0;$x<count($_POST['accounts']);$x++) {
		//delete existing link - to be sure...
		$sql = "DELETE FROM `".TABLE_PREFIX."accounts_has_groups` WHERE `group_id`='".$_POST['group_id']."' AND `account_id`='".$_POST['accounts'][$x]."'";
		if ($db->Execute($sql) === false) { 
			$negative = 'Error: '.$db->ErrorMsg();
		}
		//insert link
		$sql = "INSERT INTO `".TABLE_PREFIX."accounts_has_groups` (`account_id`,`group_id`) VALUES ('".$_POST['accounts'][$x]."','".$_POST['group_id']."')";
		if ($db->Execute($sql) === false) { 
			$negative = 'Error: '.$db->ErrorMsg();
		}
	}

//remove account from group
} elseif (isset($_GET['remove_account'])) {
	$sql = "DELETE FROM `".TABLE_PREFIX."accounts_has_groups` WHERE `account_id`='".$_GET['remove_account']."' AND `group_id`='".$_GET['from_group']."'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	} else {
		$affirmative = 'Group has been removed.';
	}


}

?>

<div id="sidemenu">
	<ul>
		<li<?php if(strlen($section) == 0) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>">Accounts</a></li>
		<li<?php if($section == 'groups') { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&section=groups">Account Groups</a></li>
	</ul>
</div>

<div id="main">
	<?php
	switch ($section) {
			
			case 'groups':
				?>
				<h2>Accounts &raquo; Groups</h2>
				<?php
				//add to group
				if (isset($_GET['add'])) {
					//get group details
					$sql = "SELECT * FROM `".TABLE_PREFIX."groups` WHERE `group_id`='".$_GET['add']."' LIMIT 1";
					$result = $db->Execute($sql);
					?>
					<form name="add" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>">
					<input type="hidden" name="group_id" value="<?php echo $_GET['add']; ?>" />
					<table border="0" class="whoa">
						<tr>
							<th>
								Add accounts to <?php echo stripslashes($result->fields['group_name']); ?> group
							</th>
						</tr>
						<?php
						$sql = "
						SELECT
							COUNT(h.`link_id`) AS `cnt`,
							a.*
						FROM
							`".TABLE_PREFIX."accounts` a
						LEFT JOIN
							`".TABLE_PREFIX."accounts_has_groups` h
						ON
							a.`account_id`=h.`account_id` AND
							h.`group_id`='".$_GET['add']."'
						GROUP BY
							a.`account_id`
						HAVING
							`cnt`=0
						ORDER BY
							a.`account_name`
						";
						$result = $db->Execute($sql);
						$recordcount = $result->RecordCount();
						if ($recordcount == 0) {
							?>
							<tr>
								<td>
									All accounts are already part of this group.
								</td>
							</tr>
							<?php
						} else {
								
							while (!$result->EOF){
								?>
								<tr>
									<td>
										<input type="checkbox" name="accounts[]" value="<?php echo $result->fields['account_id']; ?>" id="ac_<?php echo $result->fields['account_id']; ?>" /> <label for="ac_<?php echo $result->fields['account_id']; ?>"><?php echo stripslashes($result->fields['account_name']); ?></label>
									</td>
								</tr>
								<?php
								$result->MoveNext();
							}
						}
						?>
					</table>
					<p align="center">
						<input type="button" name="button" class="cross" value="Cancel" onClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>'" />
						<?php
						if ($recordcount > 0) {
							?>
							<input type="submit" class="tick" value="Add" name="add_submit" />
							<?php
						}
						?>
					</p>
					</form>
					<?php
					
				// list groups
				} else {
					
					?>
					
					<table border="0" class="whoa">
					<?php
					$sql = "SELECT * FROM `".TABLE_PREFIX."groups` ORDER BY `group_name`";
					$result = $db->Execute($sql);
					$recordcount = $result->RecordCount();
					if ($recordcount > 0) {
						?>
						<tr>
							<th>
								Group
							</th>
							<th width="16">
								e
							</th>
							<th width="16">
								d
							</th>
						</tr>
						<?php
						while (!$result->EOF){
							?>
							<tr>
								<td id="bl_<?php echo $result->fields['group_id']; ?>" rel="?mode=account_groups&edit=<?php echo $result->fields['group_id']; ?>&value=" style="font-weight:bold;">
									<?php echo stripslashes($result->fields['group_name']); ?>
								</td>
								<td width="16">
									<a id="edit_<?php echo $result->fields['group_id']; ?>" href="javascript:void(EditField('edit_<?php echo $result->fields['group_id']; ?>','bl_<?php echo $result->fields['group_id']; ?>'));" title="Edit Group"><img src="admin/images/edit.gif" border="0" width="16" height="16" alt="Edit" /></a>
								</td>
								<td width="16">
									<a href="javascript:void(AskQuestion('Do you really want to remove this group?','<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>&remove_group=<?php echo $result->fields['group_id']; ?>'));" title="Remove group"><img src="admin/images/remove.gif" border="0" width="16" height="16" alt="Remove" /></a>
								</td>
							</tr>
							<?php
							//spit out group members
							$sql2 = "SELECT a.`account_name`, a.`account_id` FROM `".TABLE_PREFIX."accounts_has_groups` h, `".TABLE_PREFIX."accounts` a WHERE a.`account_id`=h.`account_id` AND h.`group_id`='".$result->fields['group_id']."' ORDER BY a.`account_name`";
							$result2 = $db->Execute($sql2);
							while (!$result2->EOF){
								?>
								<tr>
									<td>
										&nbsp; <?php echo stripslashes($result2->fields['account_name']); ?>
									</td>
									<td width="16">&nbsp;
										
									</td>
									<td width="16">
										<a href="javascript:void(AskQuestion('Do you really want to remove this account from this group?','<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>&remove_account=<?php echo $result2->fields['account_id']; ?>&from_group=<?php echo $result->fields['group_id']; ?>'));" title="Remove account from group"><img src="admin/images/remove.gif" border="0" width="16" height="16" alt="Remove" /></a>
									</td>
										
								<?php
								$result2->MoveNext();
							}
							?>
							<tr>
								<td>
									&nbsp; <a href="<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>&add=<?php echo $result->fields['group_id']; ?>" title="Select which accounts are members of this group.">+ Add Accounts to this group</a>
								</td>
								<td width="16">
									&nbsp;
								</td>
								<td width="16">
									&nbsp;
								</td>
							</tr>
							<?php
							$result->MoveNext();
						}
					} else {
						?>
						<tr>
							<td colspan="3">There are no groups defined.</td>
						</tr>
						<?php
					}
					?>
						<tr>
							<td>
								<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>" name="new_def" id="new_def">
									<input type="text" name="new_group" value="New Group" style="width:98%;" />
								</form>
							</td>
							<td colspan="2">
								<a href="javascript:xGetElementById('new_def').submit();" title="Save New Group"><img src="admin/images/save.gif" border="0" width="16" height="16" alt="New" /></a>
							</td>
						</tr>
					</table>
					<?php
					
				}
				
				break;
			
			default:
				?>
				<h2>Accounts</h2>
				<?php
				if (isset($_GET['create'])) {
					// -------------------------------------------------------------------------------------------------------
					if (count($errors) > 0) {
						?>
						<ul class="errors">
							<?php
								for ($x=0;$x<count($errors);$x++) {
									?><li><?php echo $errors[$x]; ?></li><?php
								}
							?>
						</ul>
						<?php
					}
					
					//get list of themes from dir
					$dh = @opendir('themes/');
					$dirs = array();
					while (false !== ($file=@readdir($dh))) {
						if (substr($file,0,1)!=".") {  #skip anything that starts with a '.' i.e.:('.', '..', or any hidden file)
							if (is_dir('themes/'.$file)) {
								$dirs[]=$file;   #put directories into dirs[] and append a '/' to differentiate
							}
						}
					}
					@closedir($dh);
					?>
					
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>" name="create" method="post">
						<table border="0" class="whoa">
							<tr>
								<th colspan="2">
									Create an Account
								</th>
							</tr>
							
							<tr>
								<td>
									Account Name: *
								</td>
								<td>
									<input type="text" name="account_name" value="<?php echo stripslashes($_POST['account_name']); ?>" style="width:98%;" />
								</td>
							</tr>
							<tr>
								<td>
									Username: *
								</td>
								<td>
									<input type="text" name="account_username" value="<?php echo stripslashes($_POST['account_username']); ?>" style="width:98%;" />
								</td>
							</tr>
							<tr>
								<td>
									Password: *
								</td>
								<td>
									<input type="password" name="account_password" value="<?php echo stripslashes($_POST['account_password']); ?>" style="width:98%;" />
								</td>
							</tr>
							<tr>
								<td>
									Confirm: *
								</td>
								<td>
									<input type="password" name="account_password2" value="<?php echo stripslashes($_POST['account_password2']); ?>" style="width:98%;" />
								</td>
							</tr>
							<tr>
								<td>
									Account is disabled:
								</td>
								<td>
									<input type="radio" name="account_disabled" value="no" id="ad_no" checked="checked" /> <label for="ad_no">No</label> &nbsp;
									<input type="radio" name="account_disabled" value="yes" id="ad_yes" /> <label for="ad_yes">Yes</label>
								</td>
							</tr>
							<tr>
								<td>
									Administrator Account:
								</td>
								<td>
									<input type="radio" name="account_admin" value="no" id="aa_no" checked="checked" /> <label for="aa_no">No</label> &nbsp;
									<input type="radio" name="account_admin" value="yes" id="aa_yes" /> <label for="aa_yes">Yes</label>
								</td>
							</tr>
							
							<tr>
								<td>
									Preferred Theme:
								</td>
								<td>
									<select name="theme" style="width:98%;">
										<option value="default">Default</option>
										<?php
										for ($x=0;$x<count($dirs);$x++) {
											$s = '';
											if ($_POST['theme'] == strtolower($dirs[$x])) {
												$s = ' selected="selected"';
											}
											?>
											<option value="<?php echo $dirs[$x]; ?>"<?php echo $s; ?>><?php echo GetIntelligentName($dirs[$x]); ?></option>
											<?php
										}
										?>
									</select>
								</td>
							</tr>
							
						</table>
						<p align="center">
							<input type="button" name="button" class="cross" value="Cancel" onClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>'" />
							<input type="submit" class="tick" value="Create" name="create_submit" />
						</p>
					</form>
					
					<?php
					
				} elseif (isset($_GET['edit'])) {
					// -------------------------------------------------------------------------------------------------------
					if (count($errors) > 0) {
						?>
						<ul class="errors">
							<?php
								for ($x=0;$x<count($errors);$x++) {
									?><li><?php echo $errors[$x]; ?></li><?php
								}
							?>
						</ul>
						<?php
					}
					
					//get list of themes from dir
					$dh = @opendir('themes/');
					$dirs = array();
					while (false !== ($file=@readdir($dh))) {
						if (substr($file,0,1)!=".") {  #skip anything that starts with a '.' i.e.:('.', '..', or any hidden file)
							if (is_dir('themes/'.$file)) {
								$dirs[]=$file;   #put directories into dirs[] and append a '/' to differentiate
							}
						}
					}
					@closedir($dh);

					//get account details
					$sql = "SELECT * FROM `".TABLE_PREFIX."accounts` WHERE `account_id`='".$_GET['edit']."' LIMIT 0,1";
					$result = $db->Execute($sql);
					?>
					
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>" name="edit" method="post">
						<input type="hidden" name="account_id" value="<?php echo $result->fields['account_id']; ?>" />
						<table border="0" class="whoa">
							<tr>
								<th colspan="2">
									Edit an Account
								</th>
							</tr>
							
							<tr>
								<td>
									Account Name: *
								</td>
								<td>
									<input type="text" name="account_name" value="<?php if (isset($_POST['account_name'])) { echo stripslashes($_POST['account_name']); } else { echo stripslashes($result->fields['account_name']); } ?>" style="width:98%;" />
								</td>
							</tr>
							<tr>
								<td>
									Username: *
								</td>
								<td>
									<input type="text" name="account_username" value="<?php if (isset($_POST['account_username'])) { echo stripslashes($_POST['account_username']); } else { echo stripslashes($result->fields['account_username']); } ?>" style="width:98%;" />
								</td>
							</tr>
							<tr>
								<td>
									Password: *
								</td>
								<td>
									<input type="password" name="account_password" value="<?php if (isset($_POST['account_password'])) { echo stripslashes($_POST['account_password']); } else { echo stripslashes($result->fields['account_password']); } ?>" style="width:98%;" />
								</td>
							</tr>
							<tr>
								<td>
									Confirm: *
								</td>
								<td>
									<input type="password" name="account_password2" value="<?php if (isset($_POST['account_password2'])) { echo stripslashes($_POST['account_password2']); } else { echo stripslashes($result->fields['account_password']); } ?>" style="width:98%;" />
								</td>
							</tr>
							<tr>
								<td>
									Account is disabled:
								</td>
								<td>
									<input type="radio" name="account_disabled" value="no" id="ad_no"<?php if ((isset($_POST['account_disabled']) && $_POST['account_disabled'] == 'no') || (!isset($_POST['account_disabled']) && $result->fields['account_disabled'] == 'no')) { echo ' checked="checked"'; } ?><?php if ($result->fields['account_id'] == $afbAccount->account['account_id']) { echo ' disabled="disabled"'; } ?> /> <label for="ad_no">No</label> &nbsp;
									<input type="radio" name="account_disabled" value="yes" id="ad_yes"<?php if ((isset($_POST['account_disabled']) && $_POST['account_disabled'] == 'yes') || (!isset($_POST['account_disabled']) && $result->fields['account_disabled'] == 'yes')) { echo ' checked="checked"'; } ?><?php if ($result->fields['account_id'] == $afbAccount->account['account_id']) { echo ' disabled="disabled"'; } ?> /> <label for="ad_yes">Yes</label>
								</td>
							</tr>
							<tr>
								<td>
									Administrator Account:
								</td>
								<td>
									<input type="radio" name="account_admin" value="no" id="aa_no"<?php if ((isset($_POST['account_admin']) && $_POST['account_admin'] == 'no') || (!isset($_POST['account_admin']) && $result->fields['account_admin'] == 'no')) { echo ' checked="checked"'; } ?><?php if ($result->fields['account_id'] == $afbAccount->account['account_id']) { echo ' disabled="disabled"'; } ?> /> <label for="aa_no">No</label> &nbsp;
									<input type="radio" name="account_admin" value="yes" id="aa_yes"<?php if ((isset($_POST['account_admin']) && $_POST['account_admin'] == 'yes') || (!isset($_POST['account_admin']) && $result->fields['account_admin'] == 'yes')) { echo ' checked="checked"'; } ?><?php if ($result->fields['account_id'] == $afbAccount->account['account_id']) { echo ' disabled="disabled"'; } ?> /> <label for="aa_yes">Yes</label>
								</td>
							</tr>
							
							<tr>
								<td>
									Preferred Theme:
								</td>
								<td>
									<select name="theme" style="width:98%;">
										<option value="default">Default</option>
										<?php
										for ($x=0;$x<count($dirs);$x++) {
											$s = '';
											if ((isset($_POST['theme']) && $_POST['theme'] == strtolower($dirs[$x])) || (!isset($_POST['theme']) && $result->fields['theme'] == strtolower($dirs[$x]))) {
												$s = ' selected="selected"';
											}
											?>
											<option value="<?php echo $dirs[$x]; ?>"<?php echo $s; ?>><?php echo GetIntelligentName($dirs[$x]); ?></option>
											<?php
										}
										?>
									</select>
								</td>
							</tr>
							
						</table>
						<p align="center">
							<input type="button" name="button" class="cross" value="Cancel" onClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>'" />
							<input type="submit" class="tick" value="Update" name="edit_submit" />
						</p>
					</form>
					
					<?php
					
				} else {
					// -------------------------------------------------------------------------------------------------------
					?>
					
					<table border="0" class="whoa2">
						<tr>
							<th>
								Account
							</th>
							<th>
								Username
							</th>
							<th>
								Last Login
							</th>
							<th>
								Admin
							</th>
							<th>
								Disabled
							</th>
							<th width="16">
								e
							</th>
							<th width="16">
								d
							</th>
						</tr>
						<?php
						$sql = "SELECT * FROM `".TABLE_PREFIX."accounts` ORDER BY `account_name`";
						$result = $db->Execute($sql);
						$recordcount = $result->RecordCount();
						while (!$result->EOF){
							?>
							<tr>
								<td>
									<?php echo htmlspecialchars(stripslashes($result->fields['account_name'])); ?>
								</td>
								<td>
									<?php echo htmlspecialchars(stripslashes($result->fields['account_username'])); ?>
								</td>
								<td>
									<?php
									//get last login
									$sql2 = "SELECT * FROM `".TABLE_PREFIX."log` WHERE `account_id`='" . $result->fields['account_id'] . "' AND `log_action`='login' ORDER BY `log_timestamp` DESC LIMIT 0,1";
									$result2 = $db->Execute($sql2);
									$recordcount = $result2->RecordCount();
									if ($recordcount == 0) {
										?>-<?php
									} else {
										$last_login = $result2->fields['log_timestamp'];
										$days = GetDateDiffDays($last_login,GMT_TIME);
										if ($days == 0) {
											echo 'Today';
										} elseif ($days == 1) {
											echo 'Yesterday';
										} else {
											echo $days . ' days ago';
										}
									}
									?>
								</td>
								<td>
									<?php
									if ($result->fields['account_admin'] == 'yes') {
										?>Yes<?php
									} else {
										?>No<?php
									}						
									?>
								</td>
								<td>
									<?php
									if ($result->fields['account_disabled'] == 'yes') {
										?><span class="red">Yes</span><?php
									} else {
										?>No<?php
									}						
									?>
								</td>
								<td>
									<a href="<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&edit=<?php echo $result->fields['account_id']; ?>" title="Edit Account"><img src="admin/images/edit.gif" border="0" width="16" height="16" alt="Edit" /></a>
								</td>
								<td>
									<?php
									if (stripslashes($result->fields['account_username']) != $afbAccount->account['account_username']) {
										?>
										<a href="javascript:void(AskQuestion('Do you really want to remove this account?','<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&remove=<?php echo $result->fields['account_id']; ?>'));" title="Remove Account"><img src="admin/images/remove.gif" border="0" width="16" height="16" alt="Remove" /></a>
										<?php
									} else {
										?>&nbsp;<?php
									}
									?>
								</td>
							</tr>
							<?php
							$result->MoveNext();
						}
						?>
						<tr>
							<td colspan="7">
								<strong><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&create" title="Create another Account">Create an Account</a></strong>
							</td>
						</tr>
						
					</table>
					<?php
				}
			
				break;
			
	} //end switch
	?>			
</div>
