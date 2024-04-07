<?php if (count($links) > 0) { ?>
	<div id="sidemenu">
		<ul>
			<li class="header">Links</li>
			<?php
			for ($x=0;$x<count($links);$x++) {
				?>
				<li><a href="<?php echo $links[$x]['href']; ?>" title="<?php echo $links[$x]['name']; ?>" target="<?php echo $links[$x]['target']; ?>"><?php echo $links[$x]['name']; ?></a></li>
				<?php
			}
			?>
			
		</ul>
	</div>
<?php } ?>

<div id="main"<?php if (count($links) == 0) { echo ' class="wide"'; } ?>>
	<h2>Upload Files to <?php echo TieString($share['name'],'','/'); ?><?php echo trim(stripslashes($_GET['path']),'/'); ?></h2>	
	<?php
	if (strlen($critical_error) > 0) {
		?>
		<p class="error" align="center"><?php echo $critical_error; ?></p>
		<p align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?share=<?php echo $_GET['share']; ?>&path=<?php echo $_GET['path']; ?>">Go Back</a></p>
		<?php
	} else {
		?>
			
			<p>Files must make a total size less than <?php echo $upload_max_size; ?>, or upload will be rejected.</p>
			<?php
			if ($share_perms[$share['id']]['ul'] && $share_perms[$share['id']]['r']) {
				?>
				<p><strong>Warning:</strong> Uploading files with the same name as existing files will overwrite those existing files.</p>
				<?php
			}
			?>
			
			<?php
			if (count($good) > 0) {
				?>
				<ul class="good">
					<?php echo '<li>' . implode('</li><li>',$good) . '</li>'; ?>
				</ul>
				<?php
			}
			
			if (count($errors) > 0) {
				?>
				<ul class="errors">
					<?php echo '<li>' . implode('</li><li>',$errors) . '</li>'; ?>
				</ul>
				<?php
			}
			?>
		
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?upload&share=<?php echo $_GET['share']; ?>&path=<?php echo stripslashes($_GET['path']); ?>" enctype="multipart/form-data">
			<table border="0">
				<tr>
					<td>
						<input type="file" name="uploads[]" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="file" name="uploads[]" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="file" name="uploads[]" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="file" name="uploads[]" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="file" name="uploads[]" />
					</td>
				</tr>
				<tr>
					<td align="center">
						<input type="button" name="button" class="cross" value="Cancel" onClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>?share=<?php echo $_GET['share']; ?>&path=<?php echo $_GET['path']; ?>'" />
						<input type="submit" class="tick" name="upload_submit" value="Upload" />
					</td>
				</tr>
			</table>
			</form>
		<?php
	}
	?>
</div>