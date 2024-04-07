<?php
//new blacklist dir submission
if (isset($_POST['new_blacklist_dir'])) {
	if (strlen($_POST['new_blacklist_dir']) > 0) {
		//check if already exists.
		$sql = "SELECT COUNT(`blacklist_id`) AS `cnt` FROM `".TABLE_PREFIX."blacklist_dirs` WHERE `blacklist_dir` LIKE '".$_POST['new_blacklist_dir']."'";
		$result = $db->Execute($sql);
		if ($result->fields['cnt'] == 0) {
			//insert
			$sql = "INSERT INTO `".TABLE_PREFIX."blacklist_dirs` (`blacklist_dir`) VALUES ('" . $_POST['new_blacklist_dir'] . "')";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
		}		
	}

//delete blacklist dir
} elseif (isset($_GET['remove_dir'])) {
	$sql = "DELETE FROM `".TABLE_PREFIX."blacklist_dirs` WHERE `blacklist_id`='" . $_GET['remove_dir'] . "'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	} else {
		if ($db->Affected_Rows()) {
			$affirmative = 'Definition has been deleted';
		}
	}

//new blacklist file submission
} elseif (isset($_POST['new_blacklist_file'])) {
	if (strlen($_POST['new_blacklist_file']) > 0) {
		//check if already exists.
		$sql = "SELECT COUNT(`blacklist_id`) AS `cnt` FROM `".TABLE_PREFIX."blacklist_files` WHERE `blacklist_file` LIKE '".$_POST['new_blacklist_file']."'";
		$result = $db->Execute($sql);
		if ($result->fields['cnt'] == 0) {
			//insert
			$sql = "INSERT INTO `".TABLE_PREFIX."blacklist_files` (`blacklist_file`) VALUES ('" . $_POST['new_blacklist_file'] . "')";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
		}		
	}

//delete blacklist file
} elseif (isset($_GET['remove_file'])) {
	$sql = "DELETE FROM `".TABLE_PREFIX."blacklist_files` WHERE `blacklist_id`='" . $_GET['remove_file'] . "'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	} else {
		if ($db->Affected_Rows()) {
			$affirmative = 'Definition has been deleted';
		}
	}

//new blacklist ext submission
} elseif (isset($_POST['new_blacklist_type'])) {
	if (strlen(str_replace('.','',$_POST['new_blacklist_type'])) > 0) {
		//check if already exists.
		$sql = "SELECT COUNT(`blacklist_id`) AS `cnt` FROM `".TABLE_PREFIX."blacklist_ext` WHERE `blacklist_extension` LIKE '".str_replace('.','',$_POST['new_blacklist_type'])."'";
		$result = $db->Execute($sql);
		if ($result->fields['cnt'] == 0) {
			//insert
			$sql = "INSERT INTO `".TABLE_PREFIX."blacklist_ext` (`blacklist_extension`) VALUES ('" . str_replace('.','',$_POST['new_blacklist_type']) . "')";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
		}		
	}

//delete blacklist ext
} elseif (isset($_GET['remove_type'])) {
	$sql = "DELETE FROM `".TABLE_PREFIX."blacklist_ext` WHERE `blacklist_id`='" . $_GET['remove_type'] . "'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	} else {
		if ($db->Affected_Rows()) {
			$affirmative = 'Definition has been deleted';
		}
	}
	
}

?>

<div id="sidemenu">
	<ul>
		<li<?php if(strlen($section) == 0) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>">Folders</a></li>
		<li<?php if($section == 'files') { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&section=files">Files</a></li>
		<li<?php if($section == 'types') { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&section=types">File Types</a></li>
	</ul>
</div>

<div id="main">
	<?php
	switch ($section) {
		
		case 'files':
			?>
			<h2>Blacklists &raquo; Files</h2>
			<table class="whoa">
			<?php
			$sql = "SELECT * FROM `".TABLE_PREFIX."blacklist_files` ORDER BY `blacklist_file`";
			$result = $db->Execute($sql);
			$recordcount = $result->RecordCount();
			if ($recordcount > 0) {
				?>
				<tr>
					<th>
						Filename
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
						<td id="bl_<?php echo $result->fields['blacklist_id']; ?>" rel="?mode=blacklist_files&edit=<?php echo $result->fields['blacklist_id']; ?>&value=">
							<?php echo stripslashes($result->fields['blacklist_file']); ?>
						</td>
						<td>
							<a id="edit_<?php echo $result->fields['blacklist_id']; ?>" href="javascript:void(EditField('edit_<?php echo $result->fields['blacklist_id']; ?>','bl_<?php echo $result->fields['blacklist_id']; ?>'));" title="Edit definition"><img src="admin/images/edit.gif" border="0" width="16" height="16" alt="Edit" /></a>
						</td>
						<td>
							<a href="javascript:void(AskQuestion('Do you really want to remove this definition?','<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>&remove_file=<?php echo $result->fields['blacklist_id']; ?>'));" title="Remove definition"><img src="admin/images/remove.gif" border="0" width="16" height="16" alt="Remove" /></a>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
			} else {
				?>
				<tr>
					<td colspan="3">There are no blacklisted filenames defined.</td>
				</tr>
				<?php
			}
			?>
				<tr>
					<td>
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>" name="new_def" id="new_def">
							<input type="text" name="new_blacklist_file" value="New Blacklist Filename" style="width:98%;" />
						</form>
					</td>
					<td colspan="2">
						<a href="javascript:xGetElementById('new_def').submit();" title="Save New definition"><img src="admin/images/save.gif" border="0" width="16" height="16" alt="New" /></a>
					</td>
				</tr>
			</table>
			<?php
			break;
			
		case 'types':
			?>
			<h2>Blacklists &raquo; File Types</h2>
			<table class="whoa">
			<?php
			$sql = "SELECT * FROM `".TABLE_PREFIX."blacklist_ext` ORDER BY `blacklist_extension`";
			$result = $db->Execute($sql);
			$recordcount = $result->RecordCount();
			if ($recordcount > 0) {
				?>
				<tr>
					<th>
						Extension
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
						<td id="bl_<?php echo $result->fields['blacklist_id']; ?>" rel="?mode=blacklist_types&edit=<?php echo $result->fields['blacklist_id']; ?>&value=">
							<?php echo stripslashes($result->fields['blacklist_extension']); ?>
						</td>
						<td>
							<a id="edit_<?php echo $result->fields['blacklist_id']; ?>" href="javascript:void(EditField('edit_<?php echo $result->fields['blacklist_id']; ?>','bl_<?php echo $result->fields['blacklist_id']; ?>'));" title="Edit definition"><img src="admin/images/edit.gif" border="0" width="16" height="16" alt="Edit" /></a>
						</td>
						<td>
							<a href="javascript:void(AskQuestion('Do you really want to remove this definition?','<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>&remove_type=<?php echo $result->fields['blacklist_id']; ?>'));" title="Remove definition"><img src="admin/images/remove.gif" border="0" width="16" height="16" alt="Remove" /></a>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
			} else {
				?>
				<tr>
					<td colspan="3">There are no blacklisted extensions defined.</td>
				</tr>
				<?php
			}
			?>
				<tr>
					<td>
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&section=<?php echo $section; ?>" name="new_def" id="new_def">
							<input type="text" name="new_blacklist_type" value="New Blacklist Extenstion" style="width:98%;" />
						</form>
					</td>
					<td colspan="2">
						<a href="javascript:xGetElementById('new_def').submit();" title="Save New definition"><img src="admin/images/save.gif" border="0" width="16" height="16" alt="New" /></a>
					</td>
				</tr>
			</table>
			<?php
			break;
			
		default:
			?>
			<h2>Blacklists &raquo; Folders</h2>
			<table class="whoa">
			<?php
			$sql = "SELECT * FROM `".TABLE_PREFIX."blacklist_dirs` ORDER BY `blacklist_dir`";
			$result = $db->Execute($sql);
			$recordcount = $result->RecordCount();
			if ($recordcount > 0) {
				?>
				<tr>
					<th>
						Folder Name
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
						<td id="bl_<?php echo $result->fields['blacklist_id']; ?>" rel="?mode=blacklist_dirs&edit=<?php echo $result->fields['blacklist_id']; ?>&value=">
							<?php echo stripslashes($result->fields['blacklist_dir']); ?>
						</td>
						<td>
							<a id="edit_<?php echo $result->fields['blacklist_id']; ?>" href="javascript:void(EditField('edit_<?php echo $result->fields['blacklist_id']; ?>','bl_<?php echo $result->fields['blacklist_id']; ?>'));" title="Edit definition"><img src="admin/images/edit.gif" border="0" width="16" height="16" alt="Edit" /></a>
						</td>
						<td>
							<a href="javascript:void(AskQuestion('Do you really want to remove this definition?','<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&remove_dir=<?php echo $result->fields['blacklist_id']; ?>'));" title="Remove definition"><img src="admin/images/remove.gif" border="0" width="16" height="16" alt="Remove" /></a>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
			} else {
				?>
				<tr>
					<td colspan="3">There are no blacklisted folders defined.</td>
				</tr>
				<?php
			}
			?>
				<tr>
					<td>
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>" name="new_def" id="new_def">
							<input type="text" name="new_blacklist_dir" value="New Blacklist Folder" style="width:98%;" />
						</form>
					</td>
					<td colspan="2">
						<a href="javascript:xGetElementById('new_def').submit();" title="Save New definition"><img src="admin/images/save.gif" border="0" width="16" height="16" alt="New" /></a>
					</td>
				</tr>
			</table>
			<?php
			break;
			
	}	
	?>
</div>
