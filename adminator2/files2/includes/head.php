<?php
//i18n
header('Content-type: text/html; charset=utf-8');
//include theme header
if (file_exists('themes/'.$theme_dir.'/head.php')) {
	include('themes/'.$theme_dir.'/head.php');
} else {
	echo '<p>Error loading <strong>themes/'.$theme_dir.'/head.php</strong>, file may not exist.</p>';
	exit();
}
?>