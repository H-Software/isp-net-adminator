<div id="sidemenu">
	<ul>
		<li<?php if (!isset($_GET['report'])) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>">Reports Home</a></li>
		<li<?php if ($_GET['report'] == 1) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&report=1">Most Popular Downloads</a></li>
		<li<?php if ($_GET['report'] == 2) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&report=2">Recent Activity</a></li>
		<li<?php if ($_GET['report'] == 3) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&report=3">Recent Logins</a></li>
		<li<?php if ($_GET['report'] == 4) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&report=4">Recent Downloads</a></li>
		<li<?php if ($_GET['report'] == 5) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&report=5">Recent Uploads</a></li>
		<li<?php if ($_GET['report'] == 6) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&report=6">Recent Deletions</a></li>
		<li<?php if ($_GET['report'] == 7) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&report=7">Links Reports</a></li>
		<?php /*
		<li<?php if ($_GET['report'] == 8) { echo ' class="selected"'; } ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>&report=8">Custom Report</a></li>
		*/ ?>
	</ul>
</div>

<div id="main">	
	<?php
	//get accounts
	$sql = "SELECT * FROM `".TABLE_PREFIX."accounts` ORDER BY `account_name`";
	$result = $db->Execute($sql);
	$accounts = array(0=>array('name'=>'Anonymous'));
	while (!$result->EOF){
		$accounts[$result->fields['account_id']] = array('name'=>stripslashes($result->fields['account_name']));
		$result->MoveNext();
	}
	
	//get shares
	$sql = "SELECT * FROM `".TABLE_PREFIX."shares` ORDER BY `share_name`";
	$result = $db->Execute($sql);
	$shares = array();
	while (!$result->EOF){
		$shares[$result->fields['share_id']] = array('name'=>stripslashes($result->fields['share_name']),'dir'=>stripslashes($result->fields['share_dir']));
		$result->MoveNext();
	}
	
	//get links
	$sql = "SELECT * FROM `".TABLE_PREFIX."links` ORDER BY `link_name`";
	$result = $db->Execute($sql);
	$links = array();
	while (!$result->EOF){
		$links[$result->fields['link_id']] = array('name'=>stripslashes($result->fields['link_name']),'href'=>stripslashes($result->fields['link_href']));
		$result->MoveNext();
	}
	
	switch ($_GET['report']) {
		
		//----------------------------------------------------------------------------------------------------------------
		//Most Popular Downloads
		case 1:
			//Most Popular Downloads
			?>
			<h2>Most Popular Downloads</h2>
			
			<table border="0" class="whoa2">
				<tr>
					<th>
						File
					</th>
					<th>
						Share
					</th>
					<th>
						Downloads
					</th>
					<th>
						Full Path
					</th>
				</tr>
				<?php
				
				$sql = "SELECT
					DISTINCT `log_data`,
					`share_id`,
					`log_ip`,
					COUNT(`log_id`) as `cnt`
				FROM
					`".TABLE_PREFIX."log`
				WHERE
					`log_action`='download'
				GROUP BY
					`log_data`,
					`share_id`
				ORDER BY
					`cnt` DESC,
					`log_data`
				LIMIT 0,50
				";
				$result = $db->Execute($sql);
				$recordcount = $result->RecordCount();
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php echo htmlspecialchars(GetFilename(stripslashes($result->fields['log_data']))); ?>
						</td>
						<td>
							<?php echo $shares[$result->fields['share_id']]['name']; ?>
						</td>
						<td>
							<?php echo $result->fields['cnt']; ?>
						</td>
						<td>
							<?php echo htmlspecialchars(trim($shares[$result->fields['share_id']]['dir'],'/') . stripslashes($result->fields['log_data'])); ?>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
				
				?>
			</table>
			
			<?php
			break;
			
		//----------------------------------------------------------------------------------------------------------------
		// Recent Activity
		case 2:
			?>
			<h2>Recent Activity</h2>
			
			<table border="0" class="whoa2">
				<tr>
					<th>
						Time
					</th>
					<th>
						Action
					</th>
					<th>
						Data
					</th>
					<th>
						Account
					</th>
					<th>
						IP
					</th>
				</tr>
				<?php
				$sql = "SELECT
					*
				FROM
					`".TABLE_PREFIX."log`
				ORDER BY
					`log_timestamp` DESC
				LIMIT 0,50
				";
				$result = $db->Execute($sql);
				$recordcount = $result->RecordCount();
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php echo date("g:i a",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
							<?php echo date("d-m-Y",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
						</td>
						<?php
						switch ($result->fields['log_action']) {
							case 'login':
								?>
								<td>Logged In</td><td>&nbsp;</td>
								<?php
								break;
							case 'logout':
								?>
								<td>Logged Out</td><td>&nbsp;</td>
								<?php
								break;
							case 'download':
								?>
								<td>Downloaded</td><td><?php echo htmlspecialchars(GetFilename(stripslashes($result->fields['log_data']))); ?></td>
								<?php
								break;
							case 'upload':
								?>
								<td>Uploaded</td><td><?php echo htmlspecialchars(GetFilename(stripslashes($result->fields['log_data']))); ?></td>
								<?php
								break;
							case 'delete':
								?>
								<td>Deleted</td><td><?php echo htmlspecialchars(GetFilename(stripslashes($result->fields['log_data']))); ?></td>
								<?php
								break;
							case 'link':
								?>
								<td>Link Clicked</td><td><?php
								if (isset($links[$result->fields['log_data']]['name'])) {
									echo $links[$result->fields['log_data']]['name'];
								} else {
									echo '(link removed)';
								}
								?></td>
								<?php
								break;
						}
						?>
						<td>
							<?php echo $accounts[$result->fields['account_id']]['name']; ?>&nbsp;
						</td>
						<td>
							<?php echo $result->fields['log_ip']; ?>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
				
				?>
			</table>
			
			<?php
			
			break;
		
		//----------------------------------------------------------------------------------------------------------------
		// Recent Logins
		case 3:
			?>
			<h2>Recent Logins</h2>
			
			<table border="0" class="whoa2">
				<tr>
					<th>
						Time
					</th>
					<th>
						Account
					</th>
					<th>
						IP
					</th>
				</tr>
				<?php
				
				$sql = "SELECT
					*
				FROM
					`".TABLE_PREFIX."log`
				WHERE
					`log_action`='login'
				ORDER BY
					`log_timestamp` DESC
				LIMIT 0,50
				";
				$result = $db->Execute($sql);
				$recordcount = $result->RecordCount();
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php echo date("g:i a",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
							<?php echo date("d-m-Y",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
						</td>
						<td>
							<?php echo $accounts[$result->fields['account_id']]['name']; ?>&nbsp;
						</td>
						<td>
							<?php echo $result->fields['log_ip']; ?>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
				
				?>
			</table>
			
			<?php
			
			break;
			
		//----------------------------------------------------------------------------------------------------------------
		// Recent Downloads
		case 4:
			?>
			<h2>Recent Downloads</h2>
			
			<table border="0" class="whoa2">
				<tr>
					<th>
						Time
					</th>
					<th>
						File
					</th>
					<th>
						Share
					</th>
					<th>
						Account
					</th>
					<th>
						IP
					</th>
				</tr>
				<?php
				
				$sql = "SELECT
					*
				FROM
					`".TABLE_PREFIX."log`
				WHERE
					`log_action`='download'
				ORDER BY
					`log_timestamp` DESC
				LIMIT 0,50
				";
				$result = $db->Execute($sql);
				$recordcount = $result->RecordCount();
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php echo date("g:i a",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
							<?php echo date("d-m-Y",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
						</td>
						<td>
							<?php echo htmlspecialchars(GetFilename(stripslashes($result->fields['log_data']))); ?>
						</td>
						<td>
							<?php echo $shares[$result->fields['share_id']]['name']; ?>
						</td>
						<td>
							<?php echo $accounts[$result->fields['account_id']]['name']; ?>&nbsp;
						</td>
						<td>
							<?php echo $result->fields['log_ip']; ?>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
				
				?>
			</table>
			
			<?php
			
			break;
			
		//----------------------------------------------------------------------------------------------------------------
		// Recent Uploads
		case 5:
			?>
			<h2>Recent Uploads</h2>
			
			<table border="0" class="whoa2">
				<tr>
					<th>
						Time
					</th>
					<th>
						File
					</th>
					<th>
						Share
					</th>
					<th>
						Account
					</th>
					<th>
						IP
					</th>
				</tr>
				<?php
				
				$sql = "SELECT
					*
				FROM
					`".TABLE_PREFIX."log`
				WHERE
					`log_action`='upload'
				ORDER BY
					`log_timestamp` DESC
				LIMIT 0,50
				";
				$result = $db->Execute($sql);
				$recordcount = $result->RecordCount();
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php echo date("g:i a",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
							<?php echo date("d-m-Y",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
						</td>
						<td>
							<?php echo htmlspecialchars(GetFilename(stripslashes($result->fields['log_data']))); ?>
						</td>
						<td>
							<?php echo $shares[$result->fields['share_id']]['name']; ?>
						</td>
						<td>
							<?php echo $accounts[$result->fields['account_id']]['name']; ?>&nbsp;
						</td>
						<td>
							<?php echo $result->fields['log_ip']; ?>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
				
				?>
			</table>
			
			<?php
			
			break;
			
		//----------------------------------------------------------------------------------------------------------------
		// Recent Deletions
		case 6:
			?>
			<h2>Recent Deletions</h2>
			
			<table border="0" class="whoa2">
				<tr>
					<th>
						Time
					</th>
					<th>
						File
					</th>
					<th>
						Share
					</th>
					<th>
						Account
					</th>
					<th>
						IP
					</th>
				</tr>
				<?php
				
				$sql = "SELECT
					*
				FROM
					`".TABLE_PREFIX."log`
				WHERE
					`log_action`='delete'
				ORDER BY
					`log_timestamp` DESC
				LIMIT 0,50
				";
				$result = $db->Execute($sql);
				$recordcount = $result->RecordCount();
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php echo date("g:i a",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
							<?php echo date("d-m-Y",($result->fields['log_timestamp']+TZ_OFFSET)); ?>
						</td>
						<td>
							<?php echo htmlspecialchars(GetFilename(stripslashes($result->fields['log_data']))); ?>
						</td>
						<td>
							<?php echo $shares[$result->fields['share_id']]['name']; ?>
						</td>
						<td>
							<?php echo $accounts[$result->fields['account_id']]['name']; ?>
						</td>
						<td>
							<?php echo $result->fields['log_ip']; ?>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
				
				?>
			</table>
			
			<?php
			
			break;
			
		//----------------------------------------------------------------------------------------------------------------
		// Links Report
		case 7:
			?>
			<h2>Links Report</h2>
			
			<table border="0" class="whoa2">
				<tr>
					<th>
						Link
					</th>
					<th>
						Hits
					</th>
				</tr>
				<?php
				
				$sql = "SELECT
					DISTINCT `log_data`,
					COUNT(`log_id`) as `cnt`
				FROM
					`".TABLE_PREFIX."log`
				WHERE
					`log_action`='link'
				GROUP BY
					`log_data`
				ORDER BY
					`cnt` DESC,
					`log_data`
				";
				$result = $db->Execute($sql);
				$recordcount = $result->RecordCount();
				while (!$result->EOF){
					?>
					<tr>
						<td>
							<?php
							if (isset($links[$result->fields['log_data']]['name'])) {
								echo $links[$result->fields['log_data']]['name'];
							} else {
								echo '(link removed)';
							}
							?>
						</td>
						<td>
							<?php echo $result->fields['cnt']; ?>
						</td>
					</tr>
					<?php
					$result->MoveNext();
				}
				
				?>
			</table>
			
			<?php
			
			break;
			
		//----------------------------------------------------------------------------------------------------------------
		// Custom Report
		case 8:
			?>
			<h2>Custom Report</h2>
			
			
			
			<?php
			/*
			
			show me:
			
			-links
			-downloads
			-uplaods
			-deletions
			-logins
			
			from:
			
			-specific groups
			-specific users
			
			
			*/
			
			
			break;

		//----------------------------------------------------------------------------------------------------------------
		default:
			?>
			<h2>Reports</h2>
			<?php
			break;
	
	}
	?>	
</div>
