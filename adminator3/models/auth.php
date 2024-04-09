<?php

class auth_service{

    var $conn_mysql;

    var $smarty;

    var $user_level;

    var $page_level_id; // IDcko, dle ktereho zjistime, jestli user ma dostatecny level

    var $page_level_id_custom; // IDcko ala page_level_id ale pro jinou stranku/sub-page

    function __construct($conn_mysql, $smarty) {
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
    }
  
    function check_login(){

        start_ses();

        $cl = check_login();

        if( $cl[0] == "false" ){ 
            //wrong login ...
        
            $this->smarty->assign("page_title","Adminator3 :: chybný login");
            $this->smarty->assign("body",$cl[1]);

            $last_page = last_page();
            $this->smarty->assign("last_page",$last_page);

            $this->smarty->display('index-nologin.tpl');

            exit;
        }
    }

    function check_level($page_level_id_custom = 0, $display_no_level_page = false){
        global $level;

        if(intval($page_level_id_custom) > 0){
            $pl = $page_level_id_custom;
        }
        else{
            $pl = $this->page_level_id;
        }
        
        $rs = check_level($level,$pl);

        if( $rs === false and $display_no_level_page === true) {
            // neni level
         
            $this->smarty->assign("page_title","Adminator3 - chybny level");
            $this->smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br> (current_level: " . $level . ")");
            
            $this->smarty->display('index-nolevel.tpl');
        
            exit;
        }
        elseif($rs === false and $display_no_level_page === CURLOPT_SSL_FALSESTART){
            return false;
        }
        else{
            true;
        }
    }

    function check_all(){
        $this->check_login();
        $this->check_level();
    }
}    