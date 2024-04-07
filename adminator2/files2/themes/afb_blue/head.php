<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo htmlspecialchars($settings['site_name']); ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="Robots" content="index, follow" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="<?php echo BASEURL; ?>css.php?screen" rel="stylesheet" type="text/css" media="screen, projection" />
	<link href="<?php echo BASEURL; ?>css.php?print" rel="stylesheet" type="text/css" media="print" />
	<!--[if IE 7]>
	<link href="<?php echo BASEURL; ?>css.php?ie7" rel="stylesheet" type="text/css" media="screen, projection" />
	<![endif]-->
	<!--[if IE 6]>
	<link href="<?php echo BASEURL; ?>css.php?ie6" rel="stylesheet" type="text/css" media="screen, projection" />
	<![endif]-->
	<!--[if lt IE 6]>
	<link href="<?php echo BASEURL; ?>css.php?ie5" rel="stylesheet" type="text/css" media="screen, projection" />
	<![endif]-->	
	<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/x.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/header.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/sarissa/sarissa.js"></script>
	<?php
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'msie 6.0') == false) {
		?>
		<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/ajax.js"></script>
		<?php
	} else {
		?>
		<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/ajax_ie6.js"></script>
		<?php
	}
	?>
	<script language="javascript" type="text/javascript" src="<?php echo THEMEDIR; ?>jscript.js"></script>	
</head>

<body id="body">

	<div id="header">
		<h1><?php echo htmlspecialchars($settings['site_name']); ?></h1>
		<p id="tagline"><?php echo htmlspecialchars($settings['site_tagline']); ?></p>
	</div>