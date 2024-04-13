<?php

namespace App\Auth;

use App\Models\User;
use App\Models\PageLevel;

/**
 * Auth
 *
 * @author    Haven Shen <havenshen@gmail.com>
 * @copyright    Copyright (c) Haven Shen
 */
class Auth
{
	var $page_level_id;

	public function user()
	{
		return User::find(isset($_SESSION['user']) ? $_SESSION['user'] : 0);
	}

	public function check()
	{
		return isset($_SESSION['user']);
	}

	public function attempt($email, $password)
	{
		$user = User::where('email', $email)->first();

		if (! $user) {
			return false;
		}

		if (password_verify($password, $user->password)) {
			$_SESSION['user'] = $user->id;
			return true;
		}

		return false;
	}

	public function logout()
	{
		unset($_SESSION['user']);
	}

    public function getUserEmail()
    {
        $rs = User::find(isset($_SESSION['user']) ? $_SESSION['user'] : 0, ['email']);
        $a = $rs->toArray();
		return $a['email'];
    }

	public function getUserLevel()
	{
		$rs = User::find(isset($_SESSION['user']) ? $_SESSION['user'] : 0, ['level']);
		$a = $rs->toArray();
		return $a['level'];
	}

	function checkLevel($logger, $page_level_id_custom = 0, $display_no_level_page = true)
    {

        // co mame
        // v promeny level mame level prihlaseneho uzivatele
        // databazi levelu pro jednotlivy stranky

        // co chceme
        // porovnat level uzivatele s prislusnym levelem
        // stranky podle jejiho id

		$user_level = $this->getUserLevel();


        $logger->addInfo("auth\check_level: called with
                                    [page_level_id_custom => " . $page_level_id_custom
                                    . ", page_level_id => " . $this->page_level_id
                                    . ", user_level => " . $user_level
                                    . "]");

        if(intval($page_level_id_custom) > 0){
            $pl = $page_level_id_custom;
        }
        else{
            $pl = $this->page_level_id;
        }

        $page_level_rs = $this->find_page_level($logger,$pl);
        if($page_level_rs === false or !is_int($page_level_rs)){
            $rs = false;
        }
        elseif($user_level >= $page_level_rs){
            $rs = true; 
        }
        else{
            $rs = false;
        }

        $logger->addInfo("Auth\check_level: find_page_level: pl: " . $pl . ", retval: " . var_export($page_level_rs, true));
        $logger->addInfo("Auth\check_level: result: " . var_export($rs, true));

        if( $rs === false) {
            // user nema potrebny level
			return false;
        }
        else{
            return true;
        }
    }

	function find_page_level($logger,$page_id)
    {

        $page_level = 0;

        $rs = PageLevel::find(isset($page_id) ? $page_id : 0, ['level']);
		if(is_object($rs))
        {
            $a = $rs->toArray();
            $page_level = $a['level'];
        }

        $logger->addInfo("auth\\find_page_level: find result: " . var_export($page_level, true));

        if($page_level > 0){
            return $page_level;
        }
        else{
            return false;
        }
    }
}