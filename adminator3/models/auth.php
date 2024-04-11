<?php

class auth_service{

    var $conn_mysql;

    var $smarty;
    var $logger;

    var $user_nick;

    var $user_level;

    var $user_sid;

    var $page_level_id; // IDcko, dle ktereho zjistime, jestli user ma dostatecny level

    var $page_level_id_cxustom; // IDcko ala page_level_id ale pro jinou stranku/sub-page

    var $check_auth_no_die;

    function __construct($conn_mysql, $smarty, $logger) {
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
    }
  
    function check_login(){

        list($sid, $level, $nick) = start_ses();

        $this->user_nick = $nick;
        $this->user_level = $level;
        $this->user_sid = $sid;

        $this->logger->addInfo("auth\check_login: start_ses: result: "
            . "[nick => " . $nick
            . ", level => " . $level
            . ", sid => " . $sid
            . "]");

        $cl = check_login();
        $this->logger->addInfo("auth\check_login retval: ".var_export($cl, true));

        if($this->check_auth_no_die === true){
            $this->logger->addWarning("auth\check_login: enabled check_auth_no_die");
            return true;
        }

        if( $cl[0] == "false"){ 
            //wrong login ...
        
            $this->smarty->assign("page_title","Adminator3 :: chybný login");
            $this->smarty->assign("body",$cl[1]);

            $last_page = last_page();
            $this->smarty->assign("last_page",$last_page);

            $this->smarty->display('index-nologin.tpl');

            exit;
        }
    }

    function check_level($page_level_id_custom = 0, $display_no_level_page = true){

        // co mame
        // v promeny level mame level prihlaseneho uzivatele
        // databazi levelu pro jednotlivy stranky

        // co chceme
        // porovnat level uzivatele s prislusnym levelem
        // stranky podle jejiho id

        $this->logger->addInfo("auth\check_level: called with
                                    [page_level_id_custom => " . $page_level_id_custom
                                    . ", page_level_id => " . $this->page_level_id
                                    . ", user_level => " . $this->user_level
                                    . "]");

        if(intval($page_level_id_custom) > 0){
            $pl = $page_level_id_custom;
        }
        else{
            $pl = $this->page_level_id;
        }

        $page_level_rs = $this->find_page_level($pl);
        if($page_level_rs === false or !is_int($page_level_rs)){
            $rs = false;
        }
        elseif($this->user_level >= $page_level_rs){
            $rs = true; 
        }
        else{
            $rs = false;
        }

        $this->logger->addInfo("Auth\check_level: find_page_level: pl: " . $pl . ", retval: " . var_export($page_level_rs, true));
        $this->logger->addInfo("Auth\check_level: result: " . var_export($rs, true));

        if( $rs === false and $display_no_level_page === true) {
            // user nema potrebny level a nechceme pokracovat
            $this->smarty->assign("page_title","Adminator3 - chybny level");
            $this->smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br> (current_level: " . $this->user_level . ")");
            $this->smarty->display('index-nolevel.tpl');
        
            exit;
        }
        elseif($rs === false and $display_no_level_page === false){
            return false;
        }
        else{
            return true;
        }
    }

    function find_page_level($page_id){
        try {
            $dotaz = $this->conn_mysql->query("SELECT level FROM leveling WHERE id = '".intval($page_id)."' ");
            $radku = $dotaz->num_rows;
        } catch (Exception $e) {
            die ("<h2 style=\"color: red; \">Check level Failed: Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        $this->logger->addInfo("auth\\find_page_level: num_rows: " . $radku);

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

    function check_all(){
        $this->check_login();
        $this->check_level();
    }
}    