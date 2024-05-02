<?php

// this class is used probably only for non-slim pages (print, etc..)

use Cartalyst\Sentinel\Native\Facades\Sentinel;

class auth_service
{
    public $conn_mysql;

    public $smarty;
    public $logger;

    public $user_nick;

    public $user_level;

    public $user_sid;

    public $page_level_id; // IDcko, dle ktereho zjistime, jestli user ma dostatecny level

    public $page_level_id_cxustom; // IDcko ala page_level_id ale pro jinou stranku/sub-page

    public $check_auth_no_die;

    public function __construct($container, $conn_mysql, $smarty, $logger)
    {
        $this->container = $container;
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
    }

    public function checkLevel($page_level_id = 0, $adminator = null)
    {

        if(is_object($adminator)) {
            $a = $adminator;
        } else {
            $a = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
        }

        $auth_identity = Sentinel::getUser()->email;
        $this->logger->debug("adminatorController\\check_level getIdentity: ".var_export($auth_identity, true));

        if ($page_level_id == 0) {
            $this->renderNoLogin();
            return false;
        }

        $a->page_level_id = $page_level_id;
        $a->userIdentityUsername = $auth_identity;

        $checkLevel = $a->checkLevel();

        $this->logger->info("adminatorController\checkLevel: checkLevel result: ".var_export($checkLevel, true));

        if($checkLevel === false) {
            $this->renderNoLogin();
            return false;
        }
    }

    public function renderNoLogin()
    {
        $this->smarty->assign("page_title", "Adminator3 - chybny level");

        // $this->header();

        $this->smarty->assign("body", "<br>Neopravneny pristup /chyba pristupu. STOP <br>");
        $this->smarty->display('global/no-level.tpl');

        exit;
    }
}
