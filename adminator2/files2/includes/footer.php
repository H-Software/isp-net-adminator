<?php
//include theme footer
if (file_exists('themes/'.$theme_dir.'/footer.php')) {
	include('themes/'.$theme_dir.'/footer.php');
} else {
	echo '<p>Error loading <strong>themes/'.$theme_dir.'/footer.php</strong>, file may not exist.</p>';
	exit();
}
?>