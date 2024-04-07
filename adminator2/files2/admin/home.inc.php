<div id="home">
	<h2>Administration Home</h2>
	
	<p><strong>Welcome <?php if ($afbAccount->account['total_logins'] > 0) { echo 'back '; } echo $afbAccount->account['account_name']; ?>!</strong></p>
	<?php
	if ($afbAccount->account['last_login_timestamp'] > 0) {
		?>
		<p>Your last login was on <?php echo date("l, jS F, Y",$afbAccount->account['last_login_timestamp']); ?> at <?php echo date("g:i a",$afbAccount->account['last_login_timestamp']); ?></p>
		<?php
	}
	if ($afbAccount->account['total_logins'] > 0) {
		?>
		<p>You have logged in a total of <?php echo $afbAccount->account['total_logins']; ?> times</p>
		<?php
	}
	
	ob_flush;
	ob_end_flush;

	//attempt to retrieve update information from official remote site.
	$info = GetXML('http://ajaxfb.jc21.com/version_check_xml.php?install_id='.$settings['app_installation_id']);
	if ($info) {
		//check if update is required.
		if (
			trim($info['XMLRESPONSE']['VERSION']['MAJOR']) > $settings['app_version_major'] ||
			(trim($info['XMLRESPONSE']['VERSION']['MAJOR']) <= $settings['app_version_major'] && trim($info['XMLRESPONSE']['VERSION']['MINOR']) > $settings['app_version_minor']) ||
			(trim($info['XMLRESPONSE']['VERSION']['MAJOR']) <= $settings['app_version_major'] && trim($info['XMLRESPONSE']['VERSION']['MINOR']) <= $settings['app_version_minor']  && trim($info['XMLRESPONSE']['VERSION']['REVISION']) > $settings['app_version_revision']))
		{
			?>
			<h2>A New version is available!</h2>
			<p><strong>Version <?php echo trim($info['XMLRESPONSE']['VERSION']['MAJOR']); ?>.<?php echo trim($info['XMLRESPONSE']['VERSION']['MINOR']); ?>.<?php echo trim($info['XMLRESPONSE']['VERSION']['REVISION']); ?> has been released.</strong></p>
			<p><a href="<?php echo str_replace('[major]',$settings['app_version_major'],str_replace('[minor]',$settings['app_version_minor'],str_replace('[revision]',$settings['app_version_revision'],trim($info['XMLRESPONSE']['UPDATE'])))); ?>">Click here for changelog and download information.</a></p>
			<?php
		}
	}
	?>
	
	<p class="clear">&nbsp;</p>
	
</div>
