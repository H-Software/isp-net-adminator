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
		<?php if (count($links) > 0) { ?>
			<h2>File Explorer</h2>
		<?php } ?>
		<ul id="shares">
			<?php
			for ($x=0;$x<count($shares);$x++) {
				?>
				<li class="share" id="share_<?php echo $shares[$x]['id']; ?>"><a href="javascript:void(null);" title="<?php echo $shares[$x]['description']; ?>" onclick="ExpandFolder(this,'<?php echo $shares[$x]['id']; ?>');"><?php echo $shares[$x]['name']; ?></a>
				<?php
				if ($share_perms[$shares[$x]['id']]['ul']) {
					?>
					<span class="upload"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?upload&share=<?php echo $shares[$x]['id']; ?>&path=/" title="Upload files to <?php echo $shares[$x]['name']; ?>">Upload</a></span></li>
					<?php
				}
			}
			?>
		</ul>
		<noscript>
			<p class="error">Your browser is not capable of Javascript.</p>
			<p class="error">In addition, you should get the very best browser for your operating system. We only use the latest technology
			here, so it's only fitting you get the latest software to view it.</p>
		</noscript>
	</div>