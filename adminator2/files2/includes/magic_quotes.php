<?php
/*
This script should be included to emulate magic quotes on.
If magic quotes are already on, we don't apply any changes to the variables.
*/

//this will set all other input data (from databases etc) to have slashes.
//set_magic_quotes_runtime(TRUE);

if (!get_magic_quotes_gpc()) {
	/*
	All these global variables are not slash-encoded by default,
	because magic_quotes_gpc is not set by default!
	(And magic_quotes_gpc affects more than just $_GET, $_POST, and $_COOKIE)
	*/
	$_SERVER = addslashesArray($_SERVER);
	$_GET = addslashesArray($_GET);
	$_POST = addslashesArray($_POST);
	$_COOKIE = addslashesArray($_COOKIE);
	$_FILES = addslashesArray($_FILES);
	$_ENV = addslashesArray($_ENV);
	$_REQUEST = addslashesArray($_REQUEST);
	$HTTP_SERVER_VARS = addslashesArray($HTTP_SERVER_VARS);
	$HTTP_GET_VARS = addslashesArray($HTTP_GET_VARS);
	$HTTP_POST_VARS = addslashesArray($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = addslashesArray($HTTP_COOKIE_VARS);
	$HTTP_POST_FILES = addslashesArray($HTTP_POST_FILES);
	$HTTP_ENV_VARS = addslashesArray($HTTP_ENV_VARS);
	if (isset($_SESSION)) { #These are unconfirmed (?)
		$_SESSION = addslashesArray($_SESSION, '');
		$HTTP_SESSION_VARS = addslashesArray($HTTP_SESSION_VARS, '');
	}
}

/*
The $GLOBALS array is also slash-encoded, but when all the above are
changed, $GLOBALS is updated to reflect those changes.  (Therefore
$GLOBALS should never be modified directly).  $GLOBALS also contains
infinite recursion, so it's dangerous...
*/
?>