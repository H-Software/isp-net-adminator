<?php

namespace App\Auth;

use App\Models\User;

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

	public function getUserLevel()
	{
		$rs = User::find(isset($_SESSION['user']) ? $_SESSION['user'] : 0, ['level']);
		$a = $rs->toArray();
		return $a['level'];
	}

	function checkLevel($logger, $page_level_id_custom = 0, $display_no_level_page = true){

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

        if( $rs === false and $display_no_level_page === true) {
            // user nema potrebny level a nechceme pokracovat
            // $this->smarty->assign("page_title","Adminator3 - chybny level");
            // $this->smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br> (current_level: " . $user_level . ")");
            // $this->smarty->display('index-nolevel.tpl');
        
            exit;
        }
        elseif($rs === false and $display_no_level_page === false){
            return false;
        }
        else{
            return true;
        }
    }

	function find_page_level($logger,$page_id){
		global $conn_mysql;
        try {
            $dotaz = $conn_mysql->query("SELECT level FROM leveling WHERE id = '".intval($page_id)."' ");
            $radku = $dotaz->num_rows;
        } catch (Exception $e) {
            die ("<h2 style=\"color: red; \">Check level Failed: Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        $logger->addInfo("auth\\find_page_level: num_rows: " . $radku);

        if ($radku==0){ 
            return false; 
        }

        while ($data = $dotaz->fetch_array())
        { $level_stranky = intval($data["level"]); }

        if($level_stranky > 0){
            return $level_stranky;
        }
        else{
            return false;
        }
    }
}