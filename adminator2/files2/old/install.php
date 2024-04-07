<?php
session_start();
ini_set('display_errors','Off');
//ini_set('display_errors','On');
//error_reporting(E_ALL);

require_once('includes/functions_file.inc.php');
require_once('includes/magic_quotes.php');
require_once('includes/adodb/adodb.inc.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Ajax File Browser Installation</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="Copyright" content="jc21.com 2006" />
	<meta name="Robots" content="index, follow" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		body {
			font-size:100%;
			background-color:#CCC;
			font-family:"Trebuchet MS", Arial, Verdana;
			color:#666;
		}
		#wrapper {
			width:760px;
			margin:0 auto 0 auto;
		}
		h1 {
			font-family:"Century Gothic", "Trebuchet MS", "Arial Narrow", Arial, sans-serif;
			font-size:30px;
			text-transform:uppercase;
			font-weight:normal;
			margin:0;
			padding:0;
			padding-top:5px;
			color:#3399CC;
			margin-bottom:10px;
			text-align:left;
		}
		h2 {
			font-family:"Century Gothic", "Trebuchet MS", "Arial Narrow", Arial, sans-serif;
			font-size:15px;
			text-transform:uppercase;
			text-align:left;
			font-weight:normal;
			margin:0;
			padding:0;
			color:#336699;
			border-bottom:1px solid #336699;
		}
		#main {
			border:1px solid #336699;
			background-color:#CBDCF8;
			padding:10px;
			font-size:14px;
		}
		.error {
			color:#f00;
			text-align:center;
		}
		table {
			margin:10px auto 10px auto;
		}
		.button {
			border:1px solid #336699;
			background-color:#CCC;
			color:#3399CC;
		}
	</style>
</head>

<body id="body">
	<div id="wrapper">
		<h1>Ajax FB Installation</h1>
		<div id="main">
			<?php
			
			//check for config file.
			if (file_exists('includes/config.inc.php')) {
				//bad
				echo '<h2>Error</h2><p class="error">It appears installation is already complete. If you are experiencing problems, delete <strong>/includes/config.inc.php</strong> and then refresh this page.</p>';
				exit();
			}
			
			//now that all is well, continue...
			//clear file structure cache.
			clearstatcache();
			//check if includes is writable.
			if (!is_writable('includes/')) {
				//not writable.
				echo '<h2>Error</h2><p class="error">I do not have writable permissions for the <strong>includes</strong> directory!</p>';
				//check if we're running on windows or linux...
				if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']),"win") !== false) {
					//windows
					echo '<p>You need to allow the IIS user, or Service user full permissions to this directory.</p>';
				} else {
					//linux
					echo '<p>I need to have 777 permissions for this directory, if you are the server administrator, you should know how to do this.
					If you\'re just the site administrator, login to this site via FTP and use your client to change the permissions.</p>';
				}
				exit();
			}
			
			
			
			$step = 1;
			
			//check steps:
			if (isset($_POST['submit_step1'])) {
				//check if database settings are correct
				
				$db = NewADOConnection('mysql');
				if ($db->Connect(trim(stripslashes($_POST['db_hostname'])), trim(stripslashes($_POST['db_username'])), trim(stripslashes($_POST['db_password'])), trim(stripslashes($_POST['db_database'])))) {

/*
					$_SESSION['db'] = array(
						'hostname'=>trim(stripslashes($_POST['db_hostname'])),
						'username'=>trim(stripslashes($_POST['db_username'])),
						'password'=>trim(stripslashes($_POST['db_password'])),
						'database'=>trim(stripslashes($_POST['db_database'])),
						'prefix'=>trim(stripslashes($_POST['db_prefix']))
					);
*/

					//check that passwords match
					if ($_POST['admin_pass'] == $_POST['admin_pass2']) {
						if (strlen(trim($_POST['admin_pass'])) == 0) {
							$error = 'Password cannot be blank';
						} else {

							//ok, start making things happen!
					
							//determine baseurl
							$self = GetFilename($_SERVER['PHP_SELF']);
							if (strpos($_SERVER['REQUEST_URI'],'?') === false) {
								$baseurl = trim($_SERVER['REQUEST_URI'],$self);
							} else {
								$baseurl = trim(substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?')-1),$self);
							}
							if (strlen($baseurl) == 0) {
								$baseurl = '/';
							}
							//end determine baseurl
							$keys = array(
								'[db_hostname]'=>trim(stripslashes($_POST['db_hostname'])),
								'[db_username]'=>trim(stripslashes($_POST['db_username'])),
								'[db_password]'=>trim(stripslashes($_POST['db_password'])),
								'[db_database]'=>trim(stripslashes($_POST['db_database'])),
								'[prefix]'=>trim(stripslashes($_POST['db_prefix'])),
								'[baseurl]'=>$baseurl,
								'[admin_user]'=>stripslashes($_POST['admin_user']),
								'[admin_pass]'=>stripslashes($_POST['admin_pass']),
								'[install_id]'=>md5(time()),
								'[timezone]'=>$_POST['timezone']
							);
						
							//save config.inc.php
							if (file_exists('includes/install/config.inc.php.tpl')) {
								$config = readfile_chunked('includes/install/config.inc.php.tpl');
								$config = ApplyWildcards($config,$keys);
								//save file
								if (!$handle = fopen('includes/config.inc.php', 'a')) {
										$error = 'Could not write to <strong>includes/config.inc.php</strong>';
								} else {
									// Write $somecontent to our opened file.
									if (fwrite($handle, $config) === FALSE) {
										echo "Cannot write to file 'includes/config.inc.php'";
										exit;
									}								 
								  fclose($handle);
								
						
									//ok
									//now execute sql
									$errors = array();
									if (file_exists('includes/install/afb.xml')) {
										
										$xml = readfile_chunked('includes/install/afb.xml');
										//GET TABLES
										preg_match_all ("'<table>(.*?)</table>'si", $xml, $tables);  //find the ITEMS, and put them into an array
										$tables = $tables[1]; //set the array
					
										for ($y=0;$y<count($tables);$y++) {
											//EACH TABLE
											preg_match_all ("'<name>(.*?)</name>'si", $tables[$y], $name);
											preg_match_all ("'<test_sql>(.*?)</test_sql>'si", $tables[$y], $test_sql);
											preg_match_all ("'<table_sql>(.*?)</table_sql>'si", $tables[$y], $table_sql);
											preg_match_all ("'<table_data>(.*?)</table_data>'si", $tables[$y], $table_data);
											preg_match_all ("'<update_sql>(.*?)</update_sql>'si", $tables[$y], $update_sql);
											//set the arrays
											$name = ApplyWildcards(trim($name[1][0]),$keys);
											$test_sql = ApplyWildcards(trim($test_sql[1][0]),$keys);
											$table_sql = ApplyWildcards(trim($table_sql[1][0]),$keys);
											$table_data = ApplyWildcards($table_data[1],$keys);
											$update_sql = ApplyWildcards($update_sql[1],$keys);
											
											//TEST TABLE
											if (strlen($test_sql) > 0) {
												$result = $db->Execute($test_sql);
												if ($result) {
													$success[] = 'Found <strong>' . $name . '</strong>';
													//apply updates to existing table...
													for ($z=0;$z<count($update_sql);$z++) {
														if (strlen(trim($update_sql[$z])) > 0) {
															$result = $db->Execute($update_sql[$z]);
															if ($result) {
																$updates[] = 'Update ' . $z . ' applied to <strong>' . $name . '</strong> &nbsp; <em>' . $update_sql[$z] . '</em>';
															} else {
																$updates[] = 'Update ' . $z . ' already applied to <strong>' . $name . '</strong> &nbsp; <em>' . $update_sql[$z] . '</em>';
															}
														}
													}
									
												} else {
													//CREATE TABLE
													if (strlen($table_sql) > 0) {
														$result = $db->Execute($table_sql);
														if ($result) {
															$success[] = 'Created <strong>' . $name . '</strong>';
															//ADD DATA
															for ($z=0;$z<count($table_data);$z++) {
																if (strlen(trim($table_data[$z])) > 0) {
																	$result = $db->Execute($table_data[$z]);
																	if ($result) {
																		$success[] = 'Added data to <strong>' . $name . '</strong>';
																	} else {
																		$errors[] = 'Could not add data to <strong>' . $name . '</strong>';
																	}
																}
															}
														} else {
															$errors[] = 'Could not create <strong>' . $name . '</strong>';
														}
													}
												}
											}							
											//END EACH TABLE
										}	
										
								
										
										if (count($errors) == 0) {
						
											//ok!!
											$step = 2;
										} else {
											$error = '<ul><li>'.implode('</li><li>',$errors) . '</li></ul>';
											unlink('includes/config.inc.php');
										}
										
									} else {
										$error = 'Could not find <strong>db.sql</strong> in <strong>includes/install/</strong>';
										unlink('includes/config.inc.php');
									}
									
								}
								
							} else {
								$error = 'Could not find <strong>config.inc.php.tpl</strong> in <strong>includes/install/</strong>';
							}
							
						
							
						}
					} else {
						$error = 'Passwords do not match!';
					}

				} else {
					$step = 1;
					$error = 'Could not connect to database! Please check details, and make sure database exists.';
				}
				
			} //end if submit check
			

			
			if ($step == 1) {
				?>						
				<h2>Mysql Database Details</h2>
				<?php
					if (strlen($error) > 0) {
						?>
						<p class="error"><?php echo $error; ?></p>
						<?php
					}
				?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="step1">
					
					<table border="0" align="center" width="450">
						<tr>
							<td width="250">
								<label for="db_hostname">Database Hostname:</label>
							</td>
							<td width="200">
								<input type="text" name="db_hostname" id="db_hostname" value="<?php if (isset($_POST['db_hostname'])) { echo stripslashes($_POST['db_hostname']); } else { echo 'localhost'; } ?>" style="width:200px;" />
							</td>
						</tr>
						<tr>
							<td width="250">
								<label for="db_database">Database Name:</label>
							</td>
							<td width="200">
								<input type="text" name="db_database" id="db_database" value="<?php if (isset($_POST['db_database'])) { echo stripslashes($_POST['db_database']); } else { echo 'ajaxfb'; } ?>" style="width:200px;" />
							</td>
						</tr>
						<tr>
							<td width="250">
								<label for="db_username">Database Username:</label>
							</td>
							<td width="200">
								<input type="text" name="db_username" id="db_username" value="<?php if (isset($_POST['db_username'])) { echo stripslashes($_POST['db_username']); } else { echo 'root'; } ?>" style="width:200px;" />
							</td>
						</tr>
						<tr>
							<td width="250">
								<label for="db_password">Database Password:</label>
							</td>
							<td width="200">
								<input type="text" name="db_password" id="db_password" value="<?php if (isset($_POST['db_password'])) { echo stripslashes($_POST['db_password']); } ?>" style="width:200px;" />
							</td>
						</tr>
						<tr>
							<td width="250">
								<label for="db_prefix">Table Prefix:</label>
							</td>
							<td width="200">
								<input type="text" name="db_prefix" id="db_prefix" value="<?php if (isset($_POST['db_prefix'])) { echo stripslashes($_POST['db_prefix']); } else { echo 'afb_'; } ?>" style="width:200px;" />
							</td>
						</tr>
					</table>
					
					<h2>Admin Account</h2>
					<table border="0" align="center" width="450">
						<tr>
							<td width="250">
								<label for="admin_user">Administrator Username:</label>
							</td>
							<td width="200">
								<input type="text" name="admin_user" id="admin_user" value="<?php if (isset($_POST['admin_user'])) { echo stripslashes($_POST['admin_user']); } else { echo 'admin'; } ?>" style="width:200px;" />
							</td>
						</tr>
						<tr>
							<td width="250">
								<label for="admin_pass">Password:</label>
							</td>
							<td width="200">
								<input type="password" name="admin_pass" id="admin_pass" value="<?php if (isset($_POST['admin_pass'])) { echo stripslashes($_POST['admin_pass']); } ?>" style="width:200px;" />
							</td>
						</tr>
						<tr>
							<td width="250">
								<label for="admin_pass2">Confirm:</label>
							</td>
							<td width="200">
								<input type="password" name="admin_pass2" id="admin_pass2" value="<?php if (isset($_POST['admin_pass2'])) { echo stripslashes($_POST['admin_pass2']); } ?>" style="width:200px;" />
							</td>
						</tr>
					</table>
					
					<h2>Localisation</h2>
					<table border="0" align="center" width="450">
						<tr>
							<td width="250">
								<label for="timezone">Timezone:</label>
							</td>
							<td width="200">
								<select name="timezone" style="width:200px;">
									<?php
									//get default
									$thisoffset = date("Z");
									for ($x=-12;$x<14;$x++) {
										$s = '';
										if ((isset($_POST['timezone']) && $_POST['timezone'] == ($x*60*60)) || (!isset($_POST['timezone']) && $thisoffset == ($x*60*60))) {
											$s = ' selected="selected"';
										}
										
										$tz = 'GMT ';
										if ($x<0) {
											$tz .= '-';
										} elseif ($x>0) {
											$tz .= '+';
										}
										if (strlen($x) == 1) {
											$tz .= '0';
										}
										$tz.=$x.':00';
										
										?>
										<option value="<?php echo ($x*60*60); ?>"<?php echo $s; ?>><?php echo $tz; ?></option>
										<?php
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>&nbsp;
								
							</td>
						</tr>
						<tr>
							<td width="250">
								<input type="reset" name="reset_step1" value="Reset" class="button" />
							</td>
							<td align="right" width="200">
								<input type="submit" name="submit_step1" value="Continue &raquo;" class="button" />
							</td>
						</tr>
						
					</table>
					
				</form>
				<?php
				
			} elseif ($step == 2) {
				?>
				<h2>Congratulations!</h2>
				<p>The application has been installed!</p>
				<ul>
					<li><a href="admin.php">Login to the Administrator Area and create some shares!</a></li>
				</ul>
				<?php
			}
			?>
		</div>
	</div>
</body>
</html>