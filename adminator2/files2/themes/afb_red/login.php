<div id="login">
	<h2>Login</h2>
	
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="login">
		<table border="0">
			<tr>	
				<td>
					Username:
				</td>
				<td>
					<input type="text" name="afb_username" id="afb_username" value="<?php echo stripslashes($_REQUEST['afb_username']); ?>" />
				</td>
			</tr>
			<tr>	
				<td>
					Password:
				</td>
				<td>
					<input type="password" name="afb_password" id="afb_password" value="<?php echo stripslashes($_REQUEST['afb_password']); ?>" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;
					
				</td>
				<td>
					<?php
					if (isset($_GET['login'])) {
						?>
						<input type="button" name="button" class="cross" value="Cancel" onClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>'" />
						<?php
					}
					?>
					<input type="submit" name="login_submit" value="Login" class="tick" />
				</td>
			</tr>
			
		</table>
	</form>
	
	<?php
	if (isset($_POST['afb_username']) && isset($_POST['afb_password']) && isset($_POST['login_submit'])) {
		?>
		<p class="error">There is a problem with your details!</p>
		<?php
	}
	?>
	
</div>
