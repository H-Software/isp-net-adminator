<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo htmlspecialchars($settings['site_name']); ?> Administration</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="Copyright" content="jc21.com 2006" />
	<meta name="Robots" content="index, follow" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="<?php echo BASEURL; ?>css.php?admin&screen" rel="stylesheet" type="text/css" media="screen, projection" />
	<link href="<?php echo BASEURL; ?>css.php?admin&print" rel="stylesheet" type="text/css" media="print" />
	<!--[if IE 7]>
	<link href="<?php echo BASEURL; ?>css.php?admin&ie7" rel="stylesheet" type="text/css" media="screen, projection" />
	<![endif]-->
	<!--[if IE 6]>
	<link href="<?php echo BASEURL; ?>css.php?admin&ie6" rel="stylesheet" type="text/css" media="screen, projection" />
	<![endif]-->
	<!--[if lt IE 6]>
	<link href="<?php echo BASEURL; ?>css.php?admin&ie5" rel="stylesheet" type="text/css" media="screen, projection" />
	<![endif]-->	
	<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/x.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/header.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/sarissa/sarissa.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>_js/ajax.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASEURL; ?>admin/pageload.js"></script>
</head>

<body id="body">

	<div id="header">
		<a href="<?php echo BASEURL; ?>index.php" id="viewsite" title="Back to Main Page">View Site</a>
		<h1><?php echo htmlspecialchars($settings['site_name']); ?></h1>
		<ul id="mainmenu">
			<?php
			//iterate through main menu items
			for ($x=0;$x<count($mainmenu);$x++) {
				?>
				<li<?php if ($mainmenu[$x]['selected']) { echo ' class="selected"'; } ?>><a href="<?php echo $mainmenu[$x]['link']; ?>" title="<?php echo $mainmenu[$x]['title']; ?>"><?php echo $mainmenu[$x]['name']; ?></a></li>
				<?php
			}
			?>
		</ul>
	</div>
