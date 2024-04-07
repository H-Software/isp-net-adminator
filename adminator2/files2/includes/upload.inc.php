<?php
//check upload limits
if (ini_get('post_max_size') < ini_get('upload_max_filesize')) {
	//set post size as max..
	$upload_max_size = ini_get('post_max_size') . 'b';
} else {
	// set upload size as max..
	$upload_max_size = ini_get('upload_max_filesize') . 'b';
}


//compile path
$share = false;
for ($x=0;$x<count($shares);$x++) {
	if ($shares[$x]['id'] == $_GET['share']) {
		$share = $shares[$x];
		break;
	}
}

if ($share && $share_perms[$share['id']]['ul']) {
	$path = str_replace('\\','/',TieString($share['dir'],'','/') . trim(urldecode(stripslashes(stripslashes($_GET['path']))),'/'));
	if (CheckFolder($share['dir'],$path)) {
		//ok to go.
		$good = array();
		$errors = array();
		
		//check if upload has been sent..
		if (isset($_POST['upload_submit']) && count($_FILES['uploads']) > 0) {
			
			//get overwrite var
			$overwrite = false;
			if ($share_perms[$share['id']]['ul'] && $share_perms[$share['id']]['r']) {
				$overwrite = true;
			}
			
			//for each file
			for ($x=0;$x<count($_FILES['uploads']['tmp_name']);$x++) {
				
				if ($_FILES['uploads']['size'][$x] > 0 && $_FILES['uploads']['error'][$x] == 0) {
					
					//check if file exists.
					$newfile = $path .'/'.stripslashes($_FILES['uploads']['name'][$x]);
					
					if (file_exists($newfile) && $overwrite) {
						//delete then save the file.
						if (unlink($newfile)) {
							if (copy($_FILES['uploads']['tmp_name'][$x], $newfile)) {
								//good
								$good[] = stripslashes($_FILES['uploads']['name'][$x]) . ' has been saved';
								afbLogit('upload',$newfile,$share['id']);
							} else {
								//bad
								$errors[] = 'Could not save '.stripslashes($_FILES['uploads']['name'][$x]).', may not have system permissions.';
							}
						} else {
							$errors[] = 'Could not overwrite '.stripslashes($_FILES['uploads']['name'][$x]).', may not have system permissions.';
						}
						
					} elseif (file_exists($newfile)) {
						//generate a new name
						
						$stop = false;
						$y = 1;
						do {
							//generate a new filename, which is the same as the old filename but with a (1) on the end or whatever number
							//isn't taken.
							$filename = $_FILES['uploads']['name'][$x];
							$name = RemoveExtension($filename);
							$ext = GetExt($filename);
							$new = $name.'('.$y.').'.$ext;
							if (!file_exists($path.'/'.$new)) {
								$stop = true;
							}
							$y++;
						} while ($stop == false);
						
						if (copy($_FILES['uploads']['tmp_name'][$x], $path.'/'.$new)) {
							//good
							$good[] = $new . ' has been saved';
							afbLogit('upload',$path.'/'.$new,$share['id']);
						} else {
							//bad
							$errors[] = 'Could not save '.$new.', may not have system permissions.';
						}
						
					} else {
						//just save it!
						if (copy($_FILES['uploads']['tmp_name'][$x], $newfile)) {
							//good
							$good[] = stripslashes($_FILES['uploads']['name'][$x]) . ' has been saved';
							afbLogit('upload',$newfile,$share['id']);
						} else {
							//bad
							$errors[] = 'Could not save '.stripslashes($_FILES['uploads']['name'][$x]).', may not have system permissions.';
						}
					
					} //end whatif
					
				}  //end _files check
			} //for x
		} //end upload files
		
		
	} else {
		$critical_error = 'Folder does not exist.';
	}
} else {
	$critical_error = 'You do not have permissions to upload here.';
}

//include upload body
if (file_exists('themes/'.$theme_dir.'/upload.php')) {
	include('themes/'.$theme_dir.'/upload.php');
} else {
	echo '<p>Error loading <strong>themes/'.$theme_dir.'/body.php</strong>, file may not exist.</p>';
	exit();
}
?>