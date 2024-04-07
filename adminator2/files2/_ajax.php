<?php
//simulate a remote connection
//sleep(1);

// ------------------------------------------------------------------------------------------------
// INCLUDES
// This sets the include path relative to the current directory.
session_start();

//check for config file.
if (!file_exists('includes/config.inc.php')) {
	//send to install
	header("Content-type: text/xml");
	echo chr(60).chr(63).'xml version="1.0" encoding="utf-8" '.chr(63).chr(62);
	echo '<xmlresponse>';
	SetError('1000','Application has not been installed yet. Run install.php');
	echo '</xmlresponse>';
	exit();
}
// now that all is well, include config files.
require_once('includes/config.inc.php');
require_once('includes/settings.inc.php');
// ------------------------------------------------------------------------------------------------
header("Content-type: text/xml; charset=utf-8");
echo chr(60).chr(63).'xml version="1.0" encoding="utf-8" '.chr(63).chr(62);
?>
<xmlresponse>
	<share><?php echo $_REQUEST['share']; ?></share>
  <?php
	//try to find share..
	$share = false;
	for ($x=0;$x<count($shares);$x++) {
		if ($shares[$x]['id'] == $_REQUEST['share']) {
			$share = $shares[$x];
			break;
		}
	}
	
	if ($share) {
		
		//get path
		$path = str_replace('\\','/',TieString($share['dir'],'','/') . trim(urldecode(stripslashes($_REQUEST['path'])),'/'));
		if (isset($_GET['getfolder'])) {
			//------------------------------------------------------------------------------------------------
			// DIR
			if (CheckFolder($share['dir'],$path) && ($share_perms[$share['id']]['r'] || $share_perms[$share['id']]['ul'])) {
				//browsing a directory
				echo "\t".'<elm>'.htmlspecialchars(stripslashes($_REQUEST['elm'])).'</elm>'."\n";
				echo "\t".'<dir_path>'.htmlspecialchars(stripslashes($_REQUEST['path'])).'</dir_path>'."\n";
				
				//check if list ability exists...
				if ($share_perms[$share['id']]['r']) {
					
					
					//spit out the contents!
					$path = TieString($path,'','/');
					$dirs = array();
					$files = array();
					$total_size = 0;
					$item_count = 0;
					$max = 0;
					$dh = @opendir($path);
					while (false !== ($file=@readdir($dh))) {
						if (substr($file,0,1)!=".") {  #skip anything that starts with a '.' i.e.:('.', '..', or any hidden file)
							//echo '<check>' . htmlspecialchars($file) . ': ' . $max . ' &gt; ' . $start . ' AND ' . $item_count . ' &lt; ' . $show . '</check>';
							//if ($max >= $start && $item_count < $show) {
								if (is_dir($path.$file)) {
									$dirs[]=$file;   #put directories into dirs[] and append a '/' to differentiate
								} else {
									$files[]=$file;   #everything else goes into files[]
									$size = @filesize($path.$file);
									$total_size += $size;
								}
								//$item_count++;
							//} //end item count check
						}
					}
					@closedir($dh);
					if ($files) {
						natcasesort($files); #natural case insensitive sort
					}
					if ($dirs) {
						natcasesort($dirs);
					}
										
					//OUTPUT DIRS
					//sort dirs
					natcasesort($dirs);
					//echo
					foreach ($dirs as $key => $value) {
						if (CheckFolder($share['dir'],$path . $value)) {
							echo "\t".'<directory>'."\n";
							echo "\t\t".'<name>' . htmlspecialchars(rawurlencode($value)) . '</name>'."\n";
							echo "\t\t".'<path>' . htmlspecialchars(rawurlencode(trim($_REQUEST['path'],'/') . '/' . $value)) . '</path>'."\n";
							echo "\t\t".'<dir_id>'.md5($share['id'].trim($_REQUEST['path'],'/') . '/' . $value).'</dir_id>'."\n";
							echo "\t".'</directory>'."\n";
						}
					}
					
					//OUTPUT FILES
					//sort
					natcasesort($files);
					//echo
					foreach ($files as $key => $value) {
						//$value = htmlspecialchars(htmlentities($value));
						$size = @filesize($path . $value);
						//$total_size += $size;
						if (CheckFile($path . $value)) {
							$link = GetLink($path.$value);
							
							echo "\t".'<file>'."\n";
							echo "\t\t".'<name>' . htmlspecialchars(rawurlencode($value)) . '</name>'."\n";				
							//urlencode(htmlspecialchars(htmlentities($value,ENT_COMPAT,'UTF-8')))
							if ($link && $share_perms[$share['id']]['dl']) {
								echo "\t\t".'<link>'.htmlspecialchars('index.php?share='.$_REQUEST['share'].'&path='.rawurlencode(trim($_REQUEST['path'],'/').'/'.$value)).'</link>'."\n";
							}	else {
								//echo '<link>javascript:void(null);</link>';
								//echo '<path>' . htmlspecialchars(urlencode(trim($_REQUEST['path'],'/') . '/' . $files[$x])) . '</path>';
							}
							if (strlen(GetExt(value)) > 0) {
								echo "\t\t".'<type>' . strtolower(GetExt($value)) . '</type>'."\n";
							}
							echo "\t\t".'<size>' . GetSize($size) . '</size>'."\n";
							echo "\t\t".'<file_id>'.md5($share['id'].trim($_REQUEST['path'],'/') . '/' . $value).'</file_id>'."\n";
							echo "\t".'</file>'."\n";
						}
					}
					
					
				}	// else list ability doesn't exist.
				
				
				//here we'll send the perms in the file.
				echo "\t".'<perm_r>';
				if ($share_perms[$share['id']]['r']) { echo 'yes'; } else { echo 'no'; }
				echo '</perm_r>'."\n";
				echo "\t".'<perm_dl>';
				if ($share_perms[$share['id']]['dl']) { echo 'yes'; } else { echo 'no'; }
				echo '</perm_dl>'."\n";
				echo "\t".'<perm_ul>';
				if ($share_perms[$share['id']]['ul']) { echo 'yes'; } else { echo 'no'; }
				echo '</perm_ul>'."\n";
				echo "\t".'<perm_del>';
				if ($share_perms[$share['id']]['del']) { echo 'yes'; } else { echo 'no'; }
				echo '</perm_del>'."\n";
				
				SetError();
			} else {
				//Error!
				SetError(404,'Cannot find file or folder you\'re after.');
			}
			
			//end browsing dir
		
		//-----------------------------------------------------------------------------------------------------------------
		// delete a file
		} elseif (isset($_GET['deletefile'])) {
			//delete a file..
			
			if (CheckFolder($share['dir'],$path) && ($share_perms[$share['id']]['r'] || $share_perms[$share['id']]['ul'])) {
				//browsing a directory
				echo "\t".'<elm>'.htmlspecialchars(stripslashes($_REQUEST['elm'])).'</elm>';

				//check if we have permissions.
				if ($share_perms[$share['id']]['del']) {
					//check if file exists
					$fullpath = TieString($path,'','/').trim(urldecode(stripslashes($_REQUEST['file'])),'/');
					if (file_exists($fullpath)) {
						
						//try to delete file
						if (unlink($fullpath)) {
							//log it
							afbLogit('delete',TieString(trim(urldecode(stripslashes($_REQUEST['path'])),'/'),'','/').stripslashes(stripslashes(urldecode($_REQUEST['file']))),$share['id']);
							SetError();
						} else {
							SetError(102,'Could not delete file. May not have server permissions.');
						}
					} else {
						SetError(101,'File does not exists on server -- '.htmlspecialchars($fullpath));
					}
				} else {
					SetError(100,'You do not have permissions to delete the file.');
				}
			} else {
				SetError(404,'Cannot find file or folder you\'re after.');
			}
			
			
			//end delete file.
			
		}
		
		
	} else {
		SetError(404,'Not Found');
	}
	?> 
</xmlresponse><?php


// ----------------------------------------------------------------------------------------------------
// FUNCTIONS 
// ----------------------------------------------------------------------------------------------------

//this sets the error code node - essential for every xml document to be returned.
function SetError($code=0,$text='') {
	echo "\t<error_code>" . $code . "</error_code>\n\t<error>" . htmlspecialchars($text) . "</error>\n";
}

function GetLink($file) {
	$ext = GetExt($file);
	return true;
}

?>