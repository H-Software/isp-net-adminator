<?php
//compile path
$share = false;
for ($x=0;$x<count($shares);$x++) {
	if ($shares[$x]['id'] == $_REQUEST['share']) {
		$share = $shares[$x];
		break;
	}
}

if ($share && $share_perms[$share['id']]['dl']) {
	$path = str_replace('\\','/',TieString($share['dir'],'','/') . urldecode(stripslashes(stripslashes($_REQUEST['path']))));
	if (CheckFile($path)) {
		//get filename
		$file = GetFilename($path);
		//get type
		$ext = GetExt($path);
		
		//logit
		afbLogit('download',TieString(stripslashes(stripslashes($_REQUEST['path'])),'/'),$share['id']);
		
		//show download!
		if (ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Transfer-Encoding: binary");
		if (strlen($ext) > 0) {
			header("Content-Type: application/".$ext);
		} else {
			header("Content-Type: application/text");
		}
		//header("Content-Disposition: attachment; filename=" . urlencode($file));
		header("Content-Disposition: attachment; filename=\"" . $file."\"");
		if (isset($mimeType) && strstr($mimeType, "text/")) {
			$fp = fopen($path, "r");
		} else {
			$fp = fopen($path, "rb");
		}
		fpassthru($fp);
		exit();
	}
}
?>