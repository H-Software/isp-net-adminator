<?php
if (isset($_POST['save_submit'])) {
	foreach ($_POST as $key => $value) {
		if (strtolower($key) != 'save_submit') {
			$sql = "DELETE FROM `".TABLE_PREFIX."settings` WHERE `setting_name`='".strtolower($key)."' LIMIT 1";
			if ($db->Execute($sql) === false) { 
				$negative = 'Error: '.$db->ErrorMsg();
			} else {
				$sql = "INSERT INTO `".TABLE_PREFIX."settings` (`setting_name`,`setting_value`) VALUES ('".strtolower($key)."','".$value."')";
				if ($db->Execute($sql) === false) { 
					$negative = 'Error: '.$db->ErrorMsg();
				} else {
					$affirmative = 'Settings have been saved.';
					$settings[$key] = stripslashes($value);
				}
			}
		}
	}
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

<div id="main" class="wide">
	<h2>Settings</h2>
	
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=<?php echo $mode; ?>" name="settings">
		<table class="whoa">
			<tr>
				<th colspan="2">
					Site Details
				</th>
			</tr>
			
			<tr>
				<td>
					Site Name:
				</td>
				<td width="250">
					<input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>" style="width:250px;" />
				</td>
			</tr>
			
			<tr>
				<td>
					Tagline:
				</td>
				<td width="250">
					<input type="text" name="site_tagline" value="<?php echo $settings['site_tagline']; ?>" style="width:250px;" />
				</td>
			</tr>
			
			<tr>
				<td>
					Footer:
				</td>
				<td width="250">
					<input type="text" name="site_footer" value="<?php echo $settings['site_footer']; ?>" style="width:250px;" />
				</td>
			</tr>
			
			<tr>
				<td>
					Default Theme:
				</td>
				<td width="250">
					<select name="default_theme" style="width:250px;">
						<?php
						for ($x=0;$x<count($dirs);$x++) {
							$s = '';
							if (strtolower($settings['default_theme']) == strtolower($dirs[$x])) {
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
			
			<tr>
				<td>
					<label for="timezone">Timezone:</label>
				</td>
				<td width="250">
					<select name="timezone" style="width:250px;">
						<?php
						//get default
						$thisoffset = $settings['timezone'];
						for ($x=-12;$x<14;$x++) {
							$s = '';
							if ($thisoffset == ($x*60*60)) {
								$s = ' selected="selected"';
							}
							
							$tz = 'GMT ';
							if ($x<0) {
								$tz .= '-';
							} elseif ($x>0) {
								$tz .= '+';
							}
							if (strlen($x) == 1) {
								$tz .= '0';
							}
							$tz.=$x.':00';
							
							?>
							<option value="<?php echo ($x*60*60); ?>"<?php echo $s; ?>><?php echo $tz; ?></option>
							<?php
						}
						?>
					</select>
				</td>
			</tr>
			
		</table>
		<p align="center">
			<input type="submit" class="tick" value="Save" name="save_submit" />
		</p>
	</form>
</div>
