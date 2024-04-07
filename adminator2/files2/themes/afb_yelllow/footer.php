	<div id="footer">
		<p id="footnote">
		<?php
		if ($afbAccount->account) {
			
			?>
			Logged in as <?php echo $afbAccount->account['account_name']; ?> - <a href="<?php echo $_SERVER['PHP_SELF']; ?>?logout" title="Logout">Logout</a>
			<?php
			if ($afbAccount->account['account_admin'] == 'yes') {
				?>
				- <a href="admin.php" title="Administration Area">Admin</a>
				<?php
			}
		} else {
			?>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?login" title="User Login">Login</a>
			<?php
		}
		
		?>
		
	</p>
	<p id="footnote_right">
		<?php echo $settings['site_footer']; ?>
	</p>
	</div>
</body>
</html>