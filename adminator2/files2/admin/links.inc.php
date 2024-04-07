<?php
//create link submit
if (isset($_POST['create_submit'])) {
	//look for errors
	
	//name
	if (strlen(trim($_POST['link_name'])) == 0) {
		$errors[] = 'Link Name is required';
	}
	//url
	if (strlen(trim($_POST['link_href'])) == 0) {
		$errors[] = 'Link URL is required';
	}
	
	if (count($errors) == 0) {
		//ok to create
		
		$sql = "INSERT INTO `".TABLE_PREFIX."links` (
			`link_name`,
			`link_href`,
			`link_target`
		) VALUES (
			'".$_POST['link_name']."',
			'".$_POST['link_href']."',
			'".$_POST['link_target']."'
		)";
		if ($db->Execute($sql) === false) { 
			$errors[] = 'Error: '.$db->ErrorMsg();
			$_GET['create'] = true;
		}
	} else {
		//send back to create page
		$_GET['create'] = true;
	}
	
//edit link submit
} elseif (isset($_POST['edit_submit'])) {
	//look for errors
	
	//name
	if (strlen(trim($_POST['link_name'])) == 0) {
		$errors[] = 'Link Name is required';
	}
	//username
	if (strlen(trim($_POST['link_href'])) == 0) {
		$errors[] = 'Link URL is required';
	}
	
	if (count($errors) == 0) {
		//ok to update
		
		$sql = "UPDATE `".TABLE_PREFIX."links` SET
			`link_name`='".$_POST['link_name']."',
			`link_href`='".$_POST['link_href']."',
			`link_target`='".$_POST['link_target']."'
		WHERE `link_id`='".$_POST['link_id']."'
		LIMIT 1";
		if ($db->Execute($sql) === false) { 
			$errors[] = 'Error: '.$db->ErrorMsg();
			$_GET['edit'] = $_POST['link_id'];
		}
		
	} else {
		//send back to edit page
		$_GET['edit'] = $_POST['link_id'];
	}

//delete link submit
} elseif(isset($_GET['remove'])) {
	$sql = "DELETE FROM `".TABLE_PREFIX."links` WHERE `link_id`='" . $_GET['remove'] . "'";
	if ($db->Execute($sql) === false) { 
		$negative = 'Error: '.$db->ErrorMsg();
	} else {
		if ($db->Affected_Rows()) {
			$sql = "DELETE FROM `".TALBE_PREFIX."log` WHERE `log_action`='link' AND `log_file`='" . $_GET['remove'] . "'";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			}
			$affirmative = 'Link has been removed';
		}
	}
	
}

?>

<div id="main" class="wide">
	<h2>Links</h2>
	<?php
	
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
						Create a Link
					</th>
				</tr>
				
				<tr>
					<td>
						Link Name: *
					</td>
					<td width="250">
						<input type="text" name="link_name" value="<?php echo stripslashes($_POST['link_name']); ?>" style="width:250px;" />
					</td>
				</tr>
				<tr>
					<td>
						Link URL: *
					</td>
					<td width="250">
						<input type="text" name="link_href" value="<?php echo stripslashes($_POST['link_href']); ?>" style="width:250px;" />
					</td>
				</tr>
				<tr>
					<td>
						Open in:
					</td>
					<td width="250">
						<select name="link_target" style="width:250px;">
							<option value="_self"<?php if ($_POST['link_target'] == '_self') { echo ' selected="selected"'; } ?>>Same Window</option>
							<option value="_blank"<?php if ($_POST['link_target'] == '_blank') { echo ' selected="selected"'; } ?>>New Window</option>
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
		
		//get account details
		$sql = "SELECT * FROM `".TABLE_PREFIX."links` WHERE `link_id`='".$_GET['edit']."' LIMIT 0,1";
		$result = $db->Execute($sql);
		?>
		
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>" name="edit" method="post">
			<input type="hidden" name="link_id" value="<?php echo $result->fields['link_id']; ?>" />
			<table border="0" class="whoa">
				<tr>
					<th colspan="2">
						Edit a Link
					</th>
				</tr>
				
				<tr>
					<td>
						Link Name: *
					</td>
					<td width="250">
						<input type="text" name="link_name" value="<?php if (isset($_POST['link_name'])) { echo stripslashes($_POST['link_name']); } else { echo stripslashes($result->fields['link_name']); } ?>" style="width:250px;" />
					</td>
				</tr>
				<tr>
					<td>
						Link URL: *
					</td>
					<td width="250">
						<input type="text" name="link_href" value="<?php if (isset($_POST['link_href'])) { echo stripslashes($_POST['link_href']); } else { echo stripslashes($result->fields['link_href']); } ?>" style="width:250px;" />
					</td>
				</tr>
				<tr>
					<td>
						Open in:
					</td>
					<td width="250">
						<select name="link_target" style="width:250px;">
							<option value="_self"<?php if ((isset($_POST['link_target']) && $_POST['link_target'] == '_self') || (!isset($_POST['link_target']) && $result->fields['link_target'] == '_self')) { echo ' selected="selected"'; } ?>>Same Window</option>
							<option value="_blank"<?php if ((isset($_POST['link_target']) && $_POST['link_target'] == '_blank') || (!isset($_POST['link_target']) && $result->fields['link_target'] == '_blank')) { echo ' selected="selected"'; } ?>>New Window</option>
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
		?>
			
		<table border="0" class="whoa">
			<tr>
				<th>
					Link Name
				</th>
				<th>
					Location
				</th>
				<th>
					Hits
				</th>
				<th width="16">
					e
				</th>
				<th width="16">
					d
				</th>
			</tr>
			<?php
			$sql = "SELECT * FROM `".TABLE_PREFIX."links` ORDER BY `link_name`";
			$result = $db->Execute($sql);
			$recordcount = $result->RecordCount();
			while (!$result->EOF){
				?>
				<tr>
					<td>
						<?php echo htmlspecialchars(stripslashes($result->fields['link_name'])); ?>
					</td>
					<td>
						<?php echo htmlspecialchars(stripslashes($result->fields['link_href'])); ?>
					</td>
					<td>
						<?php
						//get hits
						$sql2 = "SELECT COUNT(`log_id`) AS `cnt` FROM `".TABLE_PREFIX."log` WHERE `log_action`='link' AND `log_data`='" . $result->fields['link_id'] . "'";
						$result2 = $db->Execute($sql2);
						echo $result2->fields['cnt'];					
						?>
					</td>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&edit=<?php echo $result->fields['link_id']; ?>" title="Edit Link"><img src="admin/images/edit.gif" border="0" width="16" height="16" alt="Edit" /></a>
					</td>
					<td>
						<a href="javascript:void(AskQuestion('Do you really want to remove this link?','<?php echo $_SERVER['PHP_SELF'];?>?mode=<?php echo $mode; ?>&remove=<?php echo $result->fields['link_id']; ?>'));" title="Remove Link"><img src="admin/images/remove.gif" border="0" width="16" height="16" alt="Remove" /></a>
					</td>
				</tr>
				<?php
				$result->MoveNext();
			}
			?>
			<tr>
				<td colspan="6">
					<strong><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&create" title="Create another Link">Create a Link</a></strong>
				</td>
			</tr>
			
		</table>
		
		<?php
	}
	?>
</div>
