<?php
	//this file loads the css file from the theme dir.
	session_start();
	header("Content-Type: text/css");
	//include config file.
	require_once('includes/config.inc.php');
	require_once('includes/settings.inc.php');
	
	if (isset($_GET['admin'])) {
		//get admin styles
		if (isset($_GET['screen'])) {
			$file = 'screen.css';
		} elseif (isset($_GET['print'])) {
			$file = 'print.css';
		} elseif (isset($_GET['ie5'])) {
			$file = 'ie5.css';
		} elseif (isset($_GET['ie6'])) {
			$file = 'ie6.css';
		} elseif (isset($_GET['ie7'])) {
			$file = 'ie7.css';
		}
		$file = 'admin/' . $file;
		
	} else {
		
		if (isset($_GET['screen'])) {
			$file = 'screen.css';
		} elseif (isset($_GET['print'])) {
			$file = 'print.css';
		} elseif (isset($_GET['ie5'])) {
			$file = 'ie5.css';
		} elseif (isset($_GET['ie6'])) {
			$file = 'ie6.css';
		} elseif (isset($_GET['ie7'])) {
			$file = 'ie7.css';
		}
		$file = 'themes/' . $theme_dir . '/' . $file;
	}	
	
	
	//check if file exists, load file, change some directories, and viola!
	if (file_exists($file)) {
		//file exists
		$contents = readfile_chunked($file);
		//apply keys
		$keys = array(
			'[themedir]'=>BASEURL.'themes/'.$theme_dir.'/',
			'[baseurl]'=>BASEURL
		);
		//change contents
		$contents = ApplyWildcards($contents,$keys);
		echo $contents;
	}	
?>
