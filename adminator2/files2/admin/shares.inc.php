<?php
//create share submit
if (isset($_POST['create_submit'])) {
	//look for errors
	
	//name
	if (strlen(trim($_POST['share_name'])) == 0) {
		$errors[] = 'Share Name is required';
	}
	//directory
	if (strlen(trim($_POST['share_dir'])) == 0) {
		$errors[] = 'Directory is required';
	} else {
		//check if directory exists
		$dir = TieString(str_replace('\\','/',stripslashes($_POST['share_dir'])),'','/');
		
		if (!is_dir($dir)) {
			$errors[] = 'Directory <span class="blue">'.$dir.'</span> does not exist';
		}
	}
	
	
	if (count($errors) == 0) {
		//ok to create
		
		$sql = "INSERT INTO `".TABLE_PREFIX."shares` (
			`share_name`,
			`share_dir`
		) VALUES (
			'".$_POST['share_name']."',
			'".addslashes($dir)."'
		)";
		if ($db->Execute($sql) === false) { 
			$errors[] = 'Error: '.$db->ErrorMsg();
			$_GET['create'] = true;
		} else {
			//get insert id
			$sql = "SELECT `share_id` FROM `".TABLE_PREFIX."shares` WHERE `share_name`='".$_POST['share_name']."' ORDER BY `share_id` DESC LIMIT 0,1";
			$result = $db->Execute($sql);
			$insert_id = $result->fields['share_id'];
			$_GET['perms'] = $insert_id;
			
			
		
		}
		
	} else {
		//send back to create page
		$_GET['create'] = true;
	}

//edit share submit
} elseif (isset($_POST['edit_submit'])) {
	//look for errors
	

	//name
	if (strlen(trim($_POST['share_name'])) == 0) {
		$errors[] = 'Share Name is required';
	}
	//directory
	if (strlen(trim($_POST['share_dir'])) == 0) {
		$errors[] = 'Directory is required';
	} else {
		//check if directory exists
		$dir = TieString(str_replace('\\','/',stripslashes($_POST['share_dir'])),'','/');
		
		if (!is_dir($dir)) {
			$errors[] = 'Directory <span class="blue">'.$dir.'</span> does not exist';
		}
	}
		
	if (count($errors) == 0) {
		//ok to create
		
		$sql = "UPDATE `".TABLE_PREFIX."shares` SET
			`share_name`='".$_POST['share_name']."',
			`share_dir`='".addslashes($dir)."'
		WHERE `share_id`='".$_POST['share_id']."' LIMIT 1
		";
		if ($db->Execute($sql) === false) { 
			$errors[] = 'Error: '.$db->ErrorMsg();
			$_GET['edit'] = $_POST['share_id'];
		}
		
	} else {
		//send back to create page
		$_GET['edit'] = $_POST['share_id'];
	}
	
//remove share submit
} elseif (isset($_GET['remove'])) {
	$sql = "DELETE FROM `".TABLE_PREFIX."shares` WHERE `share_id`='" . $_GET['remove'] . "'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	} else {
		if ($db->Affected_Rows()) {
			
			$sql = "DELETE FROM `".TALBE_PREFIX."accounts_has_perms` WHERE `share_id`='" . $_GET['remove'] . "'";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
			$sql = "DELETE FROM `".TALBE_PREFIX."groups_has_perms` WHERE `share_id`='" . $_GET['remove'] . "'";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
			
			$affirmative = 'Share has been removed';
		}
	}

//perms
} elseif (isset($_POST['perms_submit'])) {
	
	//delete all permissions prior
	$sql = "DELETE FROM `".TABLE_PREFIX."accounts_has_perms` WHERE `share_id`='".$_POST['share_id']."'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	}
	$sql = "DELETE FROM `".TABLE_PREFIX."groups_has_perms` WHERE `share_id`='".$_POST['share_id']."'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	}
	
	//re-insert
	//iterate through groups
	$sql = "SELECT `group_id` FROM `".TABLE_PREFIX."groups`";
	$result = $db->Execute($sql);
	while (!$result->EOF){
		$r = 'no';
		$dl = 'no';
		$ul = 'no';
		$del = 'no';
		
		if ($_POST['read']['g_'.$result->fields['group_id']] == 'yes') {
			$r = 'yes';
		}
		if ($_POST['download']['g_'.$result->fields['group_id']] == 'yes') {
			$dl = 'yes';
		}
		if ($_POST['upload']['g_'.$result->fields['group_id']] == 'yes') {
			$ul = 'yes';
		}
		if ($_POST['delete']['g_'.$result->fields['group_id']] == 'yes') {
			$del = 'yes';
		}
		
		//insert
		$sql = "INSERT INTO `".TABLE_PREFIX."groups_has_perms` (
			`group_id`,
			`share_id`,
			`perm_r`,
			`perm_dl`,
			`perm_ul`,
			`perm_del`
		) VALUES (
			'".$result->fields['group_id']."',
			'".$_POST['share_id']."',
			'".$r."',
			'".$dl."',
			'".$ul."',
			'".$del."'
		)";
		if ($db->Execute($sql) === false) { 
			$negative = 'Error: '.$db->ErrorMsg();
		}
		$result->MoveNext();
	} //end groups
	
	//iterate through accounts
	$sql = "SELECT `account_id` FROM `".TABLE_PREFIX."accounts`";
	$result = $db->Execute($sql);
	while (!$result->EOF){
		$r = 'no';
		$dl = 'no';
		$ul = 'no';
		$del = 'no';
		
		if ($_POST['read']['a_'.$result->fields['account_id']] == 'yes') {
			$r = 'yes';
		}
		if ($_POST['download']['a_'.$result->fields['account_id']] == 'yes') {
			$dl = 'yes';
		}
		if ($_POST['upload']['a_'.$result->fields['account_id']] == 'yes') {
			$ul = 'yes';
		}
		if ($_POST['delete']['a_'.$result->fields['account_id']] == 'yes') {
			$del = 'yes';
		}
		
		//insert
		$sql = "INSERT INTO `".TABLE_PREFIX."accounts_has_perms` (
			`account_id`,
			`share_id`,
			`perm_r`,
			`perm_dl`,
			`perm_ul`,
			`perm_del`
		) VALUES (
			'".$result->fields['account_id']."',
			'".$_POST['share_id']."',
			'".$r."',
			'".$dl."',
			'".$ul."',
			'".$del."'
		)";
		if ($db->Execute($sql) === false) { 
			$negative = 'Error: '.$db->ErrorMsg();
		}
		$result->MoveNext();
	} //end accounts
	
	//add anonymous account
	$r = 'no';
	$dl = 'no';
	$ul = 'no';
	$del = 'no';
	
	if ($_POST['read']['a_0'] == 'yes') {
		$r = 'yes';
	}
	if ($_POST['download']['a_0'] == 'yes') {
		$dl = 'yes';
	}
	if ($_POST['upload']['a_0'] == 'yes') {
		$ul = 'yes';
	}
	if ($_POST['delete']['a_0'] == 'yes') {
		$del = 'yes';
	}
	
	//insert
	$sql = "INSERT INTO `".TABLE_PREFIX."accounts_has_perms` (
		`account_id`,
		`share_id`,
		`perm_r`,
		`perm_dl`,
		`perm_ul`,
		`perm_del`
	) VALUES (
		'0',
		'".$_POST['share_id']."',
		'".$r."',
		'".$dl."',
		'".$ul."',
		'".$del."'
	)";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	}
	
}
?>
<div id="main" class="wide">
	<h2>Shares</h2>
	<?php
	
	//-------------------------------------------------------------------------------------
	if (isset($_GET['create'])) {
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
		?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>" name="create" method="post">
		<table border="0" class="whoa">
			<tr>
				<th colspan="2">
					Create a Share
				</th>
			</tr>
			
			<tr>
				<td>
					Share Name: *
				</td>
				<td width="200">
					<input type="text" name="share_name" value="<?php echo stripslashes($_POST['share_name']); ?>" style="width:200px;" />
				</td>
			</tr>
			<tr>
				<td>
					Directory: *
				</td>
				<td width="200">
					<input type="text" name="share_dir" value="<?php echo stripslashes($_POST['share_dir']); ?>" style="width:200px;" />
				</td>
			</tr>
		</table>
		
		<p align="center">
			<input type="button" name="button" class="cross" value="Cancel" onClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>'" />
			<input type="submit" class="tick" value="Create" name="create_submit" />
		</p>
		</form>
		
		<p>&nbsp;</p>
		
		<table border="0" class="whoa">
			<tr>
				<th>
					Notes abour Directory
				</th>
			</tr>
			<tr>
				<td>
					Make sure the webserver has permissions to access the directory
				</td>
			</tr>
			<tr>
				<td>
					<?php
					if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'win') !== false) {
						?>
						Directory can be Relative (to the site root, ie <span class="blue">images\</span>) or Absolute (ie <span class="blue">c:\files\</span>)
						<?php
					} else {
						?>
						Directories are case sensitive
						</td></tr><tr><td>
						Directory can be Relative (to the site root, ie <span class="blue">images/</span>) or Absolute (ie <span class="blue">/home/etc/</span>)
						<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					Trailing slash not required.
				</td>
			</tr>
		</table>	
		<?php
		
	//-------------------------------------------------------------------------------------
	} elseif (isset($_GET['edit'])) {
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
		
		//get share details
		$sql = "SELECT * FROM `".TABLE_PREFIX."shares` WHERE `share_id`='".$_GET['edit']."' LIMIT 0,1";
		$result = $db->Execute($sql);
		?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>" name="edit" method="post">
		<input type="hidden" name="share_id" value="<?php echo $result->fields['share_id']; ?>" />
		<table border="0" class="whoa">
			<tr>
				<th colspan="2">
					Edit a Share
				</th>
			</tr>
			
			<tr>
				<td>
					Share Name: *
				</td>
				<td width="200">
					<input type="text" name="share_name" value="<?php if (isset($_POST['share_name'])) { echo stripslashes($_POST['share_name']); } else { echo stripslashes($result->fields['share_name']); } ?>" style="width:200px;" />
				</td>
			</tr>
			<tr>
				<td>
					Directory: *
				</td>
				<td width="200">
					<input type="text" name="share_dir" value="<?php if (isset($_POST['share_dir'])) { echo stripslashes($_POST['share_dir']); } else { echo stripslashes($result->fields['share_dir']); } ?>" style="width:200px;" />
				</td>
			</tr>
		</table>
		
		<p align="center">
			<input type="button" name="button" class="cross" value="Cancel" onClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>'" />
			<input type="submit" class="tick" value="Update" name="edit_submit" />
		</p>
		</form>
		
		<p>&nbsp;</p>
		
		<table border="0" class="whoa">
			<tr>
				<th>
					Notes abour Directory
				</th>
			</tr>
			<tr>
				<td>
					Make sure the webserver has permissions to access the directory
				</td>
			</tr>
			<tr>
				<td>
					<?php
					if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'win') !== false) {
						?>
						Directory can be Relative (to the site root, ie <span class="blue">images\</span>) or Absolute (ie <span class="blue">c:\files\</span>)
						<?php
					} else {
						?>
						Directories are case sensitive
						</td></tr><tr><td>
						Directory can be Relative (to the site root, ie <span class="blue">images/</span>) or Absolute (ie <span class="blue">/home/etc/</span>)
						<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					Trailing slash not required.
				</td>
			</tr>
		</table>		
		<?php
		
	//-------------------------------------------------------------------------------------
	} elseif (isset($_GET['perms'])) {
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
		
		//get share details
		$sql = "SELECT * FROM `".TABLE_PREFIX."shares` WHERE `share_id`='".$_GET['perms']."' LIMIT 0,1";
		$result = $db->Execute($sql);
		$share_name = stripslashes($result->fields['share_name']);
		$share_id = $result->fields['share_id'];
		?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>" name="perms" method="post">
		<input type="hidden" name="share_id" value="<?php echo $result->fields['share_id']; ?>" />
		<table border="0" class="whoa">
			
			<?php
			$sql = "
			
			SELECT
				g.`group_name`,
				g.`group_id`,
				h.`perm_r`,
				h.`perm_dl`,
				h.`perm_ul`,
				h.`perm_del`
			FROM
				`".TABLE_PREFIX."groups` g
			LEFT JOIN
				`".TABLE_PREFIX."groups_has_perms` h
			ON
				h.`group_id`=g.`group_id` AND
				h.`share_id`='".$share_id."'
			GROUP BY
				g.`group_id`
			ORDER BY
				g.`group_name`
			";
			$result = $db->Execute($sql);
			$recordcount = $result->RecordCount();
			if ($recordcount > 0) {
				?>
				<tr>
					<th>
						Change Group Permissions for <?php echo $share_name; ?> share
					</th>
					<th>
						list
					</th>
					<th>
						dl
					</th>
					<th>
						ul
					</th>
					<th>
						del
					</th>
				</tr>
				<?php
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php echo stripslashes($result->fields['group_name']); ?>
						</td>
						<td>
							<input type="checkbox" name="read[g_<?php echo $result->fields['group_id']; ?>]" value="yes"<?php if ($result->fields['perm_r'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="download[g_<?php echo $result->fields['group_id']; ?>]" value="yes"<?php if ($result->fields['perm_dl'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="upload[g_<?php echo $result->fields['group_id']; ?>]" value="yes"<?php if ($result->fields['perm_ul'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="delete[g_<?php echo $result->fields['group_id']; ?>]" value="yes"<?php if ($result->fields['perm_del'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
			}
			
			?>
			<tr>
				<th>
					Change Account Permissions for <?php echo $share_name; ?> share
				</th>
				<th>
					list
				</th>
				<th>
					dl
				</th>
				<th>
					ul
				</th>
				<th>
					del
				</th>
			</tr>
			<?php
			//get anon settings
			$sql = "SELECT
				`perm_r`,
				`perm_dl`,
				`perm_ul`,
				`perm_del`
			FROM
				`".TABLE_PREFIX."accounts_has_perms`
			WHERE
				`account_id`='0' AND
				`share_id`='".$share_id."'
			";
			$result = $db->Execute($sql);
			$recordcount = $result->RecordCount();
			?>
			<tr>
				<td>
					Anonymous Users
				</td>
				<td>
							<input type="checkbox" name="read[a_0]" value="yes"<?php if ($result->fields['perm_r'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="download[a_0]" value="yes"<?php if ($result->fields['perm_dl'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="upload[a_0]" value="yes"<?php if ($result->fields['perm_ul'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="delete[a_0]" value="yes"<?php if ($result->fields['perm_del'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
			</tr>
				
			<?php			
			$sql = "SELECT
				a.`account_name`,
				a.`account_id`,
				h.`perm_r`,
				h.`perm_dl`,
				h.`perm_ul`,
				h.`perm_del`
			FROM
				`".TABLE_PREFIX."accounts` a
			LEFT JOIN
				`".TABLE_PREFIX."accounts_has_perms` h
			ON
				h.`account_id`=a.`account_id` AND
				h.`share_id`='".$share_id."'
			GROUP BY
				a.`account_id`
			ORDER BY
				a.`account_name`";
			$result = $db->Execute($sql);
			$recordcount = $result->RecordCount();
			if ($recordcount > 0) {
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php echo stripslashes($result->fields['account_name']); ?>
						</td>
						<td>
							<input type="checkbox" name="read[a_<?php echo $result->fields['account_id']; ?>]" value="yes"<?php if ($result->fields['perm_r'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="download[a_<?php echo $result->fields['account_id']; ?>]" value="yes"<?php if ($result->fields['perm_dl'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="upload[a_<?php echo $result->fields['account_id']; ?>]" value="yes"<?php if ($result->fields['perm_ul'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
						<td>
							<input type="checkbox" name="delete[a_<?php echo $result->fields['account_id']; ?>]" value="yes"<?php if ($result->fields['perm_del'] == 'yes') { echo ' checked="checked"'; } ?> />
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
			}
			?>
			
			
			
			
		</table>
		<p align="center">
			<input type="button" name="button" class="cross" value="Cancel" onClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>'" />
			<input type="submit" class="tick" value="Save" name="perms_submit" />
		</p>
		</form>
		
		<p>&nbsp;</p>
		
		<table border="0" class="whoa">
			<tr>
				<th>
					Notes
				</th>
			</tr>
			<tr>
				<td>
					Admin accounts automatically have all priveleges to all shares.
				</td>
			</tr>
			<tr>
				<td>
					Group and Account permissions are accumulative. If a group has read, and an account in that group only has upload,
					then that account can read and upload.
				</td>
			</tr>
			<tr>
				<td>
					Users cannot download or delete if they are not able to list aswell.
				</td>
			</tr>
			<tr>
				<td>
					Having upload permissions will allow the overwriting of files.
				</td>
			</tr>
			<tr>
				<td>
					You can create an upload only share without having list permissions. In this case, newly uploaded files
					will not overwrite existing files, but instead be renamed.
				</td>
			</tr>
		</table>
		
		
		<table border="0" class="whoa">
			<tr>
				<th colspan="2">
					Permissions Key
				</th>
			</tr>
			<tr>
				<td>
					<strong>list</strong>
				</td>
				<td>
					Can list share and files within
				</td>
			</tr>
			<tr>
				<td>
					<strong>dl</strong>
				</td>
				<td>
					Can download files from shares
				</td>
			</tr>
			<tr>
				<td>
					<strong>ul</strong>
				</td>
				<td>
					Can upload files to any directory in the share
				</td>
			</tr>
			<tr>
				<td>
					<strong>del</strong>
				</td>
				<td>
					Can delete any file or directory in the share.
				</td>
			</tr>
		</table>
		<?php
		
	//-------------------------------------------------------------------------------------
	} else {
		?>
		<table border="0" class="whoa2">
			<tr>
				<th>
					Name
				</th>
				<th>
					Location
				</th>
				<th>
					Exists
				</th>
				<th width="16">
					p
				</th>
				<th width="16">
					e
				</th>
				<th width="16">
					d
				</th>
			</tr>
			
			<?php
			$sql = "SELECT * FROM `".TABLE_PREFIX."shares` ORDER BY `share_name`";
			$result = $db->Execute($sql);
			$recordcount = $result->RecordCount();
			while (!$result->EOF){
				?>
				<tr>
					<td>
						<?php echo htmlspecialchars(stripslashes($result->fields['share_name'])); ?>
					</td>
					<td>
						<?php echo htmlspecialchars(stripslashes($result->fields['share_dir'])); ?>
					</td>
					<td>
						<?php
						if (is_dir(stripslashes($result->fields['share_dir']))) {
							?>Yes<?php
						} else {
							?><span class="red">No</span><?php
						}
						?>
					</td>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&perms=<?php echo $result->fields['share_id']; ?>" title="Change Permissions for Accounts and Groups"><img src="admin/images/key.gif" border="0" width="16" height="16" alt="Permissions" /></a>
					</td>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&edit=<?php echo $result->fields['share_id']; ?>" title="Edit Share"><img src="admin/images/edit.gif" border="0" width="16" height="16" alt="Edit" /></a>
					</td>
					<td>
						<a href="javascript:void(AskQuestion('Do you really want to remove this share?','<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&remove=<?php echo $result->fields['share_id']; ?>'));" title="Remove Share"><img src="admin/images/remove.gif" border="0" width="16" height="16" alt="Remove" /></a>
					</td>
				</tr>
				<?php
				$result->MoveNext();
			}
			?>
			<tr>
				<td colspan="6">
					<strong><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&create" title="Create another Share">Create a Share</a></strong>
				</td>
			</tr>
				
		</table>
		
		<?php
	}
	?>
</div>
