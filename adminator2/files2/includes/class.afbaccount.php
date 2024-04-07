<?php
/*
===============================================================================
  Ajax Filebrowser Account Class
-------------------------------------------------------------------------------

	Class Name:           afbAccount
	
	
  
================================================================================
*/

class afbAccount {
	
	//define vars
	var $_username;
	var $_password;
	var $db;
	var $_is_validated;
	var $account;        //stores account information.
	
  /*  ----------------------------------------------------------
			afbAccount
			----------------------------------------------------------
	    This function is called when the class is created.
	*/
	function afbAccount($username,$password) {
		//set vars
		$this->_username = $username;
		$this->_password = $password;
		$this->_is_validated = false;
		$this->account = array();
		$this->error = false;
		//reference some globals
		$this->db =& $GLOBALS['db'];
	} /*  end afbAccount function  */
	
	/*  ----------------------------------------------------------
			validate
			----------------------------------------------------------
	    This function validates the users credentials.
	*/
	function validate() {
		if (strlen($this->_username) > 0 && strlen($this->_password) > 0) {
			//check against db
			$sql = "
			SELECT
				*
			FROM
				`".TABLE_PREFIX."accounts` a
			WHERE
				`account_username`='" . addslashes($this->_username) . "'
			LIMIT 0,1";
			$result = $this->db->Execute($sql);
			$recordcount = $result->RecordCount();
			if ($recordcount > 0) {
				//check if password matches
				if ($this->_password == stripslashes($result->fields['account_password'])) {
					//everything is ok! Set the account array
					$this->account = stripslashesArray($result->fields);
					//set the validated var
					$this->_is_validated = true;
					//add some more login details to the account array
					$this->getLoginStats();
					
					//return success
					return true;
						
				} else {
					//Password is wrong
					$this->error = 'wrong_password';
					return false;
				}
			} else {
				//no records found
				$this->error = 'no_account';
				return false;
			}
			
		} else {
			return false;
		}		
	} /*  end validate function  */
	
	/*  ----------------------------------------------------------
			getLoginStats
			----------------------------------------------------------
	    This function appends the account array with login details 
	*/
	function getLoginStats() {
		if ($this->_is_validated) {
			
			$stats = array();
			//get total logins
			$sql = "SELECT COUNT(`log_id`) AS `cnt` FROM `".TABLE_PREFIX."log` WHERE `account_id`='" . $this->account['account_id'] . "' AND `log_action` LIKE 'login'";
			$result = $this->db->Execute($sql);
			$this->account['total_logins'] = $result->fields['cnt'];
			
			//get last login timestamp
			$sql = "SELECT `log_timestamp` FROM `".TABLE_PREFIX."log` WHERE `account_id`='" . $this->account['account_id'] . "' AND `log_action` LIKE 'login' ORDER BY `log_timestamp` DESC LIMIT 0,1";
			$result = $this->db->Execute($sql);
			$this->account['last_login_timestamp'] = $result->fields['log_timestamp'];
			$this->account['last_login'] = date("jS F, Y",$result->fields['log_timestamp']);
		
			return true;
		} else {
			return false;
		}
	}  /*  end getLoginStats function  */
	
	/*  ----------------------------------------------------------
			getTimeZoneOffset
			----------------------------------------------------------
	    Get's the offset is seconds for the users's timezone.
			if not specified, GMT 0 is returned.
	*/
	function getTimeZoneOffset($tz_id) {
		/*
		$sql = "
		SELECT
			`tz_offset`,
			`tz_daylight_savings`
		FROM
			`timezones`
		WHERE
			`tz_id`='" . $tz_id . "'
		LIMIT 0,1";
		$result = $this->db->execute_sql($sql);
		$recordcount = $this->db->get_num_rows($result);
		if ($recordcount > 0) {
			if ($row = $this->db->fetch_row ($result)) {
				//implement some daylight savings adjustments here. later..
				return $row['tz_offset'];
			}
		}
		*/
		return 0;
	} //end getTimeZoneOffset function
	
	
	
} // END afbAccount CLASS

?>