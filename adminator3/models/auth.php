<?php

class auth_service{

    var $conn_mysql;

    var $smarty;
    var $logger;

    var $user_level;

    var $page_level_id; // IDcko, dle ktereho zjistime, jestli user ma dostatecny level

    var $page_level_id_custom; // IDcko ala page_level_id ale pro jinou stranku/sub-page

    function __construct($conn_mysql, $smarty, $logger) {
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
    }
  
    function check_login(){

        start_ses();

        $cl = check_login();
        $this->logger->addInfo("check_login retval: ".var_export($cl));

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

        // co mame
        // v promeny level mame level prihlaseneho uzivatele
        // databazi levelu pro jednotlivy stranky

        // co chceme
        // porovnat level uzivatele s prislusnym levelem
        // stranky podle jejiho id

        global $level, $user_level;
        
        $this->logger->addInfo("check_level called 
                                    [page_level_id_custom => " . $page_level_id_custom . ","
                                    ." page_level_id => " . $this->page_level_id) . "]";

        if(intval($page_level_id_custom) > 0){
            $pl = $page_level_id_custom;
        }
        else{
            $pl = $this->page_level_id;
        }

        // $rs = check_level($level,$pl);
        $page_level_rs = $this->find_page_level($level);
        if( $page_level_rs === false){
            $rs = false;
        }
        else{
            if ( $user_level >= $page_level_rs){
                $rs = true; 
            }
            else{
                $rs = false;
            }
        }

        $this->logger->addInfo("check_level retval: " . var_export($rs));

        if( $rs === false and $display_no_level_page === true) {
            // user nema potrebny level a nechceme pokracovat
            $this->smarty->assign("page_title","Adminator3 - chybny level");
            $this->smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br> (current_level: " . $level . ")");
            $this->smarty->display('index-nolevel.tpl');
        
            exit;
        }
        elseif($rs === false and $display_no_level_page === false){
            return false;
        }
        else{
            true;
        }
    }

    function find_page_level($page_id){
        try {
            $dotaz = $this->conn_mysql->query("SELECT level FROM leveling WHERE id = '".intval($page_id)."' ");
            $radku = $dotaz->num_rows;
        } catch (Exception $e) {
            die ("<h2 style=\"color: red; \">Check level Failed: Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        if ($radku==0){ 
            return false; 
        }

        while ($data = $dotaz->fetch_array())
        { $level_stranky = $data["level"]; }

        if( is_int($level_stranky) ){
            return $level_stranky;
        }
        else{
            return false;
        }
    }

    function check_all(){
        $this->check_login();
        $this->check_level();
    }
}    