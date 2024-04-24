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

    function __construct($container, $conn_mysql, $smarty, $logger) {
        $this->container = $container;
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
    }
  
    public function checkLevel($page_level_id = 0, $adminator = null){

        if(is_object($adminator))
        {
            $a = $adminator;
        }
        else
        {
            $a = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
        }

        $auth_identity = $this->container->auth->getIdentity();
        // $this->logger->info("adminatorController\\check_level getIdentity: ".var_export( $auth_identity['username'], true));

        if ($page_level_id == 0){
            $this->renderNoLogin();
            return false;
        }

        $a->page_level_id = $page_level_id;
        $a->userIdentityUsername = $auth_identity['username'];

        $checkLevel = $a->checkLevel();
        
        $this->logger->info("adminatorController\checkLevel: checkLevel result: ".var_export($checkLevel, true));

        if($checkLevel === false){
            $this->renderNoLogin();
            return false;
        }
    }
}    