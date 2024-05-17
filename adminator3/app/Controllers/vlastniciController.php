<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Illuminate\Database\Capsule\Manager as DB;

class vlastniciController extends adminatorController
{
    public $conn_mysql;

    public $conn_pgsql;

    public $smarty;
    public $logger;

    protected $adminator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->conn_pgsql = $this->container->get('connPgsql');

        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        $this->logger->info("vlastniciController\__construct called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("vlastniciController\cat called");

        $this->checkLevel(90, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Zákazníci");

        $this->header($request, $response, $this->adminator);

        $this->smarty->assign("body", "Prosím vyberte z podkategorie výše....");

        $this->smarty->display('vlastnici/vlastnici-cat.tpl');

        return $response;
    }

    public function cross(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(92, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Zákazníci :: Rozcestnik");

        $this->header($request, $response, $this->adminator);

        $vlastnik2 = new \vlastnik2($this->container);

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnik2->csrf_html = $csrf_html;

        $rs = $vlastnik2->crossCheckVars();

        if($rs === false) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": crossCheckVars failed.");

            $this->smarty->assign("alert_type", $vlastnik2->alert_type);
            $this->smarty->assign("alert_content", $vlastnik2->alert_content);

            $this->smarty->display("vlastnici/cross-alert.tpl");
        } else {
            $rs = $vlastnik2->crossRun();
        }

        return $response;
    }
    public function search(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $bodyContent = "";

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(102, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Zákazníci :: hledání");

        $this->header($request, $response, $this->adminator);

        $vlastnikfind = new \vlastnikfind();
        $vlastnikfind->conn_mysql = $this->conn_mysql;
        $vlastnikfind->conn_pgsql = $this->conn_pgsql;
        $vlastnikfind->echo = true;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnikfind->csrf_html = $csrf_html;

        $find = $_GET["find"];
        $najdi = $_GET["najdi"];

        $form_select = intval($_GET["select"]);
        $form_razeni = intval($_GET["razeni"]);
        $form_razeni2 = intval($_GET["razeni2"]);

        // $sql = $this->conn_mysql->real_escape_string($find);

        if (empty($_GET["najdi"])) {
            $this->smarty->assign("form_find", "%");
        } else {
            $this->smarty->assign("form_find", htmlspecialchars($find));
        }

        $this->smarty->assign("form_select", $form_select);
        $this->smarty->assign("form_razeni", $form_razeni);
        $this->smarty->assign("form_razeni2", $form_razeni2);

        if(empty($_GET["find"])) {
            $body = "Zadejte výraz k vyhledání.... <br>";

            $this->smarty->assign("bodyNoData", $body);

            $this->smarty->display('vlastnici/hledani.tpl');

            return $response;
        }

        $sql = "%".htmlspecialchars($find)."%";
	    $select1 = " WHERE ( firma is  NULL OR firma = 0 ) AND ( archiv = 0 or archiv is null ) AND ";
	    $select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' OR ulice LIKE '$sql' ";
	    $select1 .= " OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";
	    
	    $select2=" OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
	    $select2 .= "OR vs LIKE '$sql' ) ";
			 
		if ( $_GET["select"] == 2){ $select3=" AND fakturacni > 0 "; }
		if ( $_GET["select"] == 3){ $select3=" AND fakturacni is NULL "; }
		if ( $_GET["select"] == 4){ $select3=" AND k_platbe = 0 "; }
		if ( $_GET["select"] == 5){ $select3=" AND k_platbe > 0 "; }
		
		if ( $_GET["select"] == 2){ $select3=" AND fakturacni > 0 "; }
		if ( $_GET["select"] == 3){ $select3=" AND fakturacni is NULL "; }
		if ( $_GET["select"] == 4){ $select3=" AND k_platbe = 0 "; }
		if ( $_GET["select"] == 5){ $select3=" AND k_platbe > 0 "; }
	
		if ( $_GET["razeni"] == 1){ $select4=" order by id_cloveka "; }
		if ( $_GET["razeni"] == 3){ $select4=" order by jmeno "; }
		if ( $_GET["razeni"] == 4){ $select4=" order by prijmeni "; }
		if ( $_GET["razeni"] == 5){ $select4=" order by ulice "; }
		if ( $_GET["razeni"] == 6){ $select4=" order by mesto "; }
		if ( $_GET["razeni"] == 14){ $select4=" order by vs "; }
		if ( $_GET["razeni"] == 15){ $select4=" order by k_platbe "; }
						 
		if ( $_GET["razeni2"] == 1){ $select5=" ASC "; }
		if ( $_GET["razeni2"] == 2){ $select5=" DESC "; }
						     
		$dotaz_source = " SELECT * FROM vlastnici ".$select1.$select2.$select3.$select4;
	  
		if ( ( strlen($select5) > 1 ) ){ $dotaz_source = $dotaz_source.$select5; }

        $bc1 = $vlastnikfind->vypis_tab(1);
        $this->smarty->assign("body1", $bc1);

        $bc2 = $vlastnikfind->vypis($sql,$dotaz_source);
        $this->smarty->assign("body2", $bc2);

        
        return $response;
    }

    public function vlastnici(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $bodyContent = "";

        $this->logger->info("vlastniciController\\vlastnici called");
        $this->checkLevel(13, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Zákazníci");

        $this->header($request, $response, $this->adminator);

        $vlastnik = new \vlastnik();
        $vlastnik->conn_mysql = $this->conn_mysql;
        $vlastnik->conn_pgsql = $this->conn_pgsql;
        $vlastnik->echo = false;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnik->csrf_html = $csrf_html;

        if ($this->adminator->checkLevel(64, false) === true) {
            $this->smarty->assign("vlastnici_export_povolen", "true");
        }

        if ($this->adminator->checkLevel(40, false) === true) {
            $this->smarty->assign("vlastnici_pridani_povoleno", "true");
        }

        $find_id = $_GET["find_id"];
        $find = $_GET["find"];

        $form_select = intval($_GET["select"]);
        $form_razeni = intval($_GET["razeni"]);
        $form_razeni2 = intval($_GET["razeni2"]);

        if((strlen($find_id) > 0)) {
            $co = 3;
            /* hledani podle id_cloveka */
            $sql = intval($find_id);
        } elseif ((strlen($find) > 0)) {
            $co = 1;
            /* hledani podle cehokoli */
            $sql = $this->conn_mysql->real_escape_string($find);
        } else {
            /* cokoli dalsiho */
        }

        if (empty($_GET["find"])) {
            $this->smarty->assign("form_find", "%");
        } else {
            $this->smarty->assign("form_find", htmlspecialchars($find));
        }

        $this->smarty->assign("select", $form_select);
        $this->smarty->assign("razeni", $form_razeni);
        $this->smarty->assign("razeni2", $form_razeni2);

        //promena pro update objektu
        if ($this->adminator->checkLevel(29, false) === true) {
            $vlastnik->objekt_update_povolen = true;
        }
        if ($this->adminator->checkLevel(33, false) === true) {
            $vlastnik->objekt_mazani_povoleno = true;
        }
        if ($this->adminator->checkLevel(34, false) === true) {
            $vlastnik->objekt_garant_akce = true;
        }

        // promeny pro mazani, zmenu vlastniku
        if ($this->adminator->checkLevel(45, false) === true) {
            $vlastnik->vlastnici_erase_povolen = true;
        }
        if ($this->adminator->checkLevel(30, false) === true) {
            $vlastnik->vlastnici_update_povolen = true;
        }

        // odendani objektu od vlastnika
        if ($this->adminator->checkLevel(49, false) === true) {
            $vlastnik->odendani_povoleno = true;
        }

        // co - co hledat, 1- podle dns, 2-podle ip , 3 - dle id_vlastnika
        if ($co == 1) {

            $sql = "%".$sql."%";
            $select1 = " WHERE firma is NULL AND ( archiv = 0 or archiv is null ) AND ";
            $select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' ";
            $select1 .= " OR ulice LIKE '$sql' OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";

            $select2 = " OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
            $select2 .= "OR vs LIKE '$sql') ";

            if ($_GET["select"] == 2) {
                $select3 = " AND fakturacni > 0 ";
            }
            if ($_GET["select"] == 3) {
                $select3 = " AND fakturacni is NULL ";
            }
            if ($_GET["select"] == 4) {
                $select3 = " AND k_platbe = 0 ";
            }
            if ($_GET["select"] == 5) {
                $select3 = " AND k_platbe > 0 ";
            }

            if ($_GET["razeni"] == 1) {
                $select4 = " order by id_cloveka ";
            }
            if ($_GET["razeni"] == 3) {
                $select4 = " order by jmeno ";
            }
            if ($_GET["razeni"] == 4) {
                $select4 = " order by prijmeni ";
            }
            if ($_GET["razeni"] == 5) {
                $select4 = " order by ulice ";
            }
            if ($_GET["razeni"] == 6) {
                $select4 = " order by mesto ";
            }
            if ($_GET["razeni"] == 14) {
                $select4 = " order by vs ";
            }
            if ($_GET["razeni"] == 15) {
                $select4 = " order by k_platbe ";
            }

            if ($_GET["razeni2"] == 1) {
                $select5 = " ASC ";
            }
            if ($_GET["razeni2"] == 2) {
                $select5 = " DESC ";
            }

            // @phpstan-ignore-next-line
            if (strlen($select4) > 1) {
                $select4 = $select4.$select5;
            }

            $dotaz_source = " SELECT * FROM vlastnici ".$select1.$select2.$select3.$select4;

            // @phpstan-ignore-next-line
        } elseif ($co == 3) {
            $dotaz_source = "SELECT * FROM vlastnici WHERE id_cloveka = '" . intval($sql) ."' AND firma is null AND ( archiv = 0 or archiv is null )";
        } else {
            $body = "<div style=\"padding-top: 20px; padding-bottom: 20px; \">Zadejte výraz k vyhledání.... </div>";

            $this->smarty->assign("body", $body);

            $this->smarty->display('vlastnici/vlastnici.tpl');

            return $response;
        }

        $list = $_GET["list"];

        $poradek = "find=".$find."&find_id=".$find_id."&najdi=".$_GET["najdi"]."&select=".$_GET["select"]."&razeni=".$_GET["razeni"]."&razeni2=".$_GET["razeni2"];

        //vytvoreni objektu
        $listovani = new \c_listing_vlastnici("./vlastnici.php?".$poradek."&menu=1", 30, $list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $dotaz_source);
        $listovani->echo = false;

        if (($list == "") || ($list == "1")) {
            $bude_chybet = 0;
        } else {
            $bude_chybet = (($list - 1) * $listovani->interval);
        }

        $interval = $listovani->interval;

        $dotaz_final = $dotaz_source." LIMIT " . intval($interval) . " OFFSET " . intval($bude_chybet) . " ";

        $bodyContent .= $listovani->listInterval();

        $bodyContent .= $vlastnik->vypis_tab(1);

        $bodyContent .= $vlastnik->vypis($sql, $co, 0, $dotaz_final);

        $bodyContent .= $vlastnik->vypis_tab(2);

        $bodyContent .= $listovani->listInterval();

        $this->smarty->assign("body", $bodyContent);

        $this->smarty->display('vlastnici/vlastnici.tpl');

        return $response;
    }

    public function vlastnici2(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("vlastniciController\\vlastnici2 called");

        $this->checkLevel(38, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Zákazníci 2");

        $this->header($request, $response, $this->adminator);

        $vlastnik2 = new \vlastnik2($this->container);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnik2->csrf_html = $csrf_html;

        // selectors form
        //
        $fs = new \App\Customer\fakturacniSkupiny($this->container);

        $select = $_GET["select"];

        if($select == 2) {
            $fu_select = "2";
        } //Pouze FU
        if($select == 3) {
            $fu_select = "1";
        } //pouze DU

        $this->smarty->assign("select", $select);
        $fakt_skupiny = $fs->show_fakt_skupiny($fu_select);

        $this->smarty->assign("fakt_skupiny", $fakt_skupiny);

        $this->smarty->assign("fakt_skupiny_selected", $_GET['fakt_skupina']);

        $this->smarty->assign("razeni", $_GET['razeni']);
        $this->smarty->assign("razeni2", $_GET['razeni2']);

        if ($this->adminator->checkLevel(63, false) === true) {
            $this->smarty->assign("vlastnici2_export_povolen", "true");
        }

        if ($this->adminator->checkLevel(40, false) === true) {
            $this->smarty->assign("vlastnici2_pridani_povoleno", "true");
        }

        if(empty($_GET["find"])) {
            $vlastnik2->form_find = "%";
        } else {
            $vlastnik2->form_find = $this->conn_mysql->real_escape_string($_GET["find"]);
        }

        // main table
        $bodyContent = $vlastnik2->listItems();

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": vlastnik2->listSql: " . var_export($vlastnik2->listSql, true));

        $this->smarty->assign("form_search_value", preg_replace('/^(%)(.*)(%)$/', '\2', $vlastnik2->listSql));

        $this->smarty->assign("body", $bodyContent);

        $this->smarty->display('vlastnici/vlastnici2.tpl');

        return $response;
    }

    public function archiv(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info("vlastniciController\\fakturacniSkupiny called");

        $this->checkLevel(82, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Zákazníci :: Archiv");

        $this->header($request, $response, $this->adminator);

        $vlastnikArchiv = new \vlastnikarchiv();
        $vlastnikArchiv->conn_mysql = $this->conn_mysql;
        $vlastnikArchiv->conn_pgsql = $this->conn_pgsql;
        $vlastnikArchiv->echo = false;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnikArchiv->csrf_html = $csrf_html;

        $bodyContent = "";

        $find_id = $_GET["find_id"];
        $find = $_GET["find"];

        $form_select = intval($_GET["select"]);
        $form_razeni = intval($_GET["razeni"]);
        $form_razeni2 = intval($_GET["razeni2"]);

        if ((strlen($find_id) > 0)) {
            $co = 3;
            /* hledani podle id_cloveka */
            $sql = intval($_GET["find_id"]);
        } elseif ((strlen($find) > 0)) {
            $co = 1;
            /* hledani podle cehokoli */
            $sql = $this->conn_mysql->real_escape_string($find);
        } else { /* cokoli dalsiho */
        }

        if (empty($_GET["find"])) {
            $this->smarty->assign("form_find", "%");
        } else {
            $this->smarty->assign("form_find", htmlspecialchars($find));
        }

        $this->smarty->assign("form_select", $form_select);
        $this->smarty->assign("form_razeni", $form_razeni);
        $this->smarty->assign("form_razeni2", $form_razeni2);

        //promena pro update objektu
        if ($this->adminator->checkLevel(29, false) === true) {
            $vlastnikArchiv->objekt_update_povolen = true;
        }
        if ($this->adminator->checkLevel(33, false) === true) {
            $vlastnikArchiv->objekt_mazani_povoleno = true;
        }
        if ($this->adminator->checkLevel(34, false) === true) {
            $vlastnikArchiv->objekt_garant_akce = true;
        }

        // promeny pro mazani, zmenu vlastniku
        if ($this->adminator->checkLevel(45, false) === true) {
            $vlastnikArchiv->vlastnici_erase_povolen = true;
        }
        if ($this->adminator->checkLevel(30, false) === true) {
            $vlastnikArchiv->vlastnici_update_povolen = true;
        }

        // odendani objektu od vlastnika
        if ($this->adminator->checkLevel(49, false) === true) {
            $vlastnikArchiv->odendani_povoleno = true;
        }

        $bodyContent .= $vlastnikArchiv->vypis_tab(1);

        if ($co == 1) {

            $sql = "%".$sql."%";
            $select1 = " WHERE archiv = '1' AND ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' OR ulice LIKE '$sql' OR mesto LIKE '$sql' ";
            $select2 = " OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' OR vs LIKE '$sql' ) ";

            if ($_GET["select"] == 2) {
                $select3 = " AND fakturacni > 0 ";
            }
            if ($_GET["select"] == 3) {
                $select3 = " AND fakturacni is NULL ";
            }
            if ($_GET["select"] == 4) {
                $select3 = " AND k_platbe = 0 ";
            }
            if ($_GET["select"] == 5) {
                $select3 = " AND k_platbe > 0 ";
            }

            if ($_GET["select"] == 2) {
                $select3 = " AND fakturacni > 0 ";
            }
            if ($_GET["select"] == 3) {
                $select3 = " AND fakturacni is NULL ";
            }
            if ($_GET["select"] == 4) {
                $select3 = " AND k_platbe = 0 ";
            }
            if ($_GET["select"] == 5) {
                $select3 = " AND k_platbe > 0 ";
            }

            if ($_GET["razeni"] == 1) {
                $select4 = " order by id_cloveka ";
            }
            if ($_GET["razeni"] == 3) {
                $select4 = " order by jmeno ";
            }
            if ($_GET["razeni"] == 4) {
                $select4 = " order by prijmeni ";
            }
            if ($_GET["razeni"] == 5) {
                $select4 = " order by ulice ";
            }
            if ($_GET["razeni"] == 6) {
                $select4 = " order by mesto ";
            }
            if ($_GET["razeni"] == 14) {
                $select4 = " order by vs ";
            }
            if ($_GET["razeni"] == 15) {
                $select4 = " order by k_platbe ";
            }

            if ($_GET["razeni2"] == 1) {
                $select5 = " ASC ";
            }
            if ($_GET["razeni2"] == 2) {
                $select5 = " DESC ";
            }

            // @phpstan-ignore-next-line
            if (strlen($select4) > 1) {
                $select4 = $select4.$select5;
            }

            $dotaz_source = " SELECT * FROM vlastnici ".$select1.$select2.$select3.$select4;
            // @phpstan-ignore-next-line
        } elseif ($co == 3) {
            $dotaz_source = "SELECT * FROM vlastnici WHERE archiv = '1' AND id_cloveka = '$sql' ";
        } else {
            $body = "Zadejte výraz k vyhledání.... <br>";

            $this->smarty->assign("body", $body);

            $this->smarty->display('vlastnici/archiv.tpl');

            return $response;
        }

        // global $list;
        $list = $_GET["list"];

        $poradek = "find=".$find."&find_id=".intval($find_id)."&najdi=".$_GET["najdi"]."&select=".$form_select."&razeni=".$form_razeni."&razeni2=".$form_razeni2;

        //vytvoreni objektu
        $listovani = new \c_listing_vlastnici2("/vlastnici/archiv?".$poradek."&menu=1", 30, $list, "", "", $dotaz_source);
        $listovani->echo = false;

        if (($list == "") || ($list == "1")) {    //pokud není list zadán nebo je první
            $bude_chybet = 0;                  //bude ve výběru sql dotazem chybet 0 záznamů
        } else {
            $bude_chybet = (($list - 1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
        }

        $interval = $listovani->interval;

        $dotaz_final = $dotaz_source." LIMIT ".$interval." OFFSET ".$bude_chybet." ";

        $this->smarty->assign("listing", $listovani->listInterval());

        $bodyContent .= $vlastnikArchiv->vypis($sql, $co, $dotaz_final);

        $bodyContent .= $vlastnikArchiv->vypis_tab(2);

        $this->smarty->assign("body", $bodyContent);

        $this->smarty->display('vlastnici/archiv.tpl');

        return $response;
    }

    public function fakturacniSkupiny(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("vlastniciController\\fakturacniSkupiny called");

        $this->checkLevel(99, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Zákazníci :: fakturační skupiny");

        $this->header($request, $response, $this->adminator);

        // list logic
        //
        $fs = new \App\Customer\fakturacniSkupiny($this->container);
        $fs_items = $fs->getItems();

        if(empty($fs_items)) {
            $this->smarty->assign("message_no_items", "Nebyly nalezeny žádné fakturační skupiny");
            $this->smarty->display('vlastnici/fakturacni-skupiny.tpl');
            return $response;
        }

        $this->smarty->assign("fs_items", $fs_items);

        // debug
        // $this->smarty->assign("fs_items_debug","<pre>" . var_export($fs_items,true). "</pre>");

        $this->smarty->display('vlastnici/fakturacni-skupiny/list.tpl');

        return $response;
    }

    public function fakturacniSkupinyAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info("vlastniciController\\fakturacniSkupinyAction called");

        $this->checkLevel(301, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: . :: fakturační skupiny :: Action");

        $this->header($request, $response, $this->adminator);

        $fs = new \App\Customer\fakturacniSkupiny($this->container);
        $fs->csrf_html = $this->generateCsrfToken($request, $response, true);
        $fs->adminator_ctl = $this->adminator;

        $fs_action_body = $fs->Action();

        $this->smarty->assign("body", $fs_action_body);

        $this->smarty->display('vlastnici/fakturacni-skupiny/action.tpl');

        return $response;
    }

}
