<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class vlastniciController extends adminatorController
{
    public \mysqli|\PDO $conn_mysql;

    public \PgSql\Connection|\PDO|null $conn_pgsql;

    public \Smarty $smarty;

    public \Monolog\Logger $logger;

    // protected $sentinel;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    public function __construct(ContainerInterface $container, $adminatorInstance = null)
    {
        $this->container = $container;
        $this->conn_mysql = $this->container->get('connMysql');
        $this->conn_pgsql = $this->container->get('connPgsql');

        $this->smarty = $this->container->get('smarty');
        $this->logger = $this->container->get('logger');
        // $this->sentinel = $this->container->get('sentinel');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        parent::__construct($container, $adminatorInstance);
    }

    public function cat(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(90)) {
            return $this->response;
        };

        $assignData = array(
            "page_title" => "Adminator3 :: Zákazníci",
            "body" => "Prosím vyberte z podkategorie výše...."
        );

        return $this->renderer->template($request, $response, 'vlastnici/vlastnici-cat.tpl', $assignData);
    }

    public function cross(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(302)) {
            return $this->response;
        };

        $assignData = ["page_title" => "Adminator3 :: Zákazníci :: Rozcestnik"];

        $vlastnik2 = new \vlastnik2($this->container);

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnik2->csrf_html = $csrf_html;

        $rs = $vlastnik2->crossCheckVars();

        if ($rs == false) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": crossCheckVars failed.");

            $assignData["alert_type"] = $vlastnik2->alert_type;
            $assignData["alert_content"] = $vlastnik2->alert_content;

            $rendererTemplateName = "vlastnici/cross-alert.tpl";
            $http_status_code = 500;
        } else {
            list($output, $http_status_code) = $vlastnik2->crossRun();
            $assignData["pageContent"] = $output;
            $rendererTemplateName = "global/empty.tpl";
        }

        return $this->renderer->template($request, $response, $rendererTemplateName, $assignData, $http_status_code);
    }

    public function search(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(102)) {
            return $this->response;
        };

        $assignData = ["page_title" => "Adminator3 :: Zákazníci :: hledání"];

        $vlastnikfind = new \vlastnikfind();
        $vlastnikfind->conn_mysql = $this->conn_mysql;
        $vlastnikfind->conn_pgsql = $this->conn_pgsql;
        $vlastnikfind->echo = false;
        $vlastnikfind->container = $this->container;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnikfind->csrf_html = $csrf_html;

        $find = $_GET["find"];
        $najdi = $_GET["najdi"];

        $form_select = intval($_GET["select"]);
        $form_razeni = intval($_GET["razeni"]);
        $form_razeni2 = intval($_GET["razeni2"]);

        // $sql = $this->conn_mysql->real_escape_string($find);

        if (empty($_GET["najdi"])) {
            $assignData["form_find"] = "%";
        } else {
            $assignData["form_find"] = htmlspecialchars($find);
        }

        $assignData["form_select"] = $form_select;
        $assignData["form_razeni"] = $form_razeni;
        $assignData["form_razeni2"] = $form_razeni2;

        if (empty($_GET["find"])) {
            $assignData["bodyNoData"] = "Zadejte výraz k vyhledání.... <br>";

            return $this->renderer->template($request, $response, 'vlastnici/hledani.tpl', $assignData);
        }

        $sql = "%".htmlspecialchars($find)."%";
        $select1 = " WHERE ( firma is  NULL OR firma = 0 ) AND ( archiv = 0 or archiv is null ) AND ";
        $select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' OR ulice LIKE '$sql' ";
        $select1 .= " OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";

        $select2 = " OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
        $select2 .= "OR vs LIKE '$sql' ) ";

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

        $dotaz_source = " SELECT * FROM vlastnici ".$select1.$select2.$select3.$select4;

        if (!empty($select5)) {
            $dotaz_source = $dotaz_source.$select5;
        }

        $bc1 = $vlastnikfind->vypis_tab(1);
        $assignData["body1"] = $bc1;

        $bc2 = $vlastnikfind->vypis($sql, $dotaz_source);
        $assignData["body2"] = $bc2;

        $sql = "".$sql."";
        $select1 = " WHERE firma is not NULL AND ( archiv = 0 or archiv is null ) AND ";
        $select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' ";
        $select1 .= " OR ulice LIKE '$sql' OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";

        $select2 = " OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
        $select2 .= " OR vs LIKE '$sql') ";

        $dotaz_source = " SELECT * FROM vlastnici ".$select1.$select2.$select3.$select4;

        if (!empty($select5)) {
            $dotaz_source = $dotaz_source.$select5;
        }

        $bc3 = $vlastnikfind->vypis($sql, $dotaz_source);
        $assignData["body3"] = $bc3;

        $sql = "".$sql."";
        $dotaz_source = "26058677";

        $bc4 = $vlastnikfind->vypis($sql, $dotaz_source, "2");
        $assignData["body4"] = $bc4;

        $bc5 = $vlastnikfind->vypis_tab(2);
        $assignData["body5"] = $bc5;

        return $this->renderer->template($request, $response, 'vlastnici/hledani.tpl', $assignData);
    }

    public function vlastnici(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(13)) {
            return $this->response;
        };

        $assignData = ["page_title" => "Adminator3 :: Zákazníci"];

        $vlastnik = new \vlastnik();
        $vlastnik->conn_mysql = $this->conn_mysql;
        $vlastnik->conn_pgsql = $this->conn_pgsql;
        $vlastnik->echo = false;
        $vlastnik->container = $this->container;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnik->csrf_html = $csrf_html;

        if ($this->adminator->checkLevel(64) === true) {
            $assignData["vlastnici_export_povolen"] = "true";
        }

        if ($this->adminator->checkLevel(40) === true) {
            $assignData["vlastnici_pridani_povoleno"] = "true";
        }

        $find_id = $_GET["find_id"];
        $find = $_GET["find"];

        $form_select = intval($_GET["select"]);
        $form_razeni = intval($_GET["razeni"]);
        $form_razeni2 = intval($_GET["razeni2"]);

        if ((strlen($find_id) > 0)) {
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
            $assignData["form_find"] = "%";
        } else {
            $assignData["form_find"] = htmlspecialchars($find);
        }

        $assignData["select"] = $form_select;
        $assignData["razeni"] = $form_razeni;
        $assignData["razeni2"] = $form_razeni2;

        //promena pro update objektu
        if ($this->adminator->checkLevel(29) === true) {
            $vlastnik->objekt_update_povolen = true;
        }
        if ($this->adminator->checkLevel(33) === true) {
            $vlastnik->objekt_mazani_povoleno = true;
        }
        if ($this->adminator->checkLevel(34) === true) {
            $vlastnik->objekt_garant_akce = true;
        }

        // promeny pro mazani, zmenu vlastniku
        if ($this->adminator->checkLevel(45) === true) {
            $vlastnik->vlastnici_erase_povolen = true;
        }
        if ($this->adminator->checkLevel(30) === true) {
            $vlastnik->vlastnici_update_povolen = true;
        }

        // odendani objektu od vlastnika
        if ($this->adminator->checkLevel(49) === true) {
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
            $assignData["body"] = "<div style=\"padding-top: 20px; padding-bottom: 20px; \">Zadejte výraz k vyhledání.... </div>";

            return $this->renderer->template($request, $response, 'vlastnici/vlastnici.tpl', $assignData);
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

        $bodyContent = $listovani->listInterval();

        $bodyContent .= $vlastnik->vypis_tab(1);

        $bodyContent .= $vlastnik->vypis($sql, $co, 0, $dotaz_final);

        $bodyContent .= $vlastnik->vypis_tab(2);

        $bodyContent .= $listovani->listInterval();

        $assignData["body"] = $bodyContent;

        return $this->renderer->template($request, $response, 'vlastnici/vlastnici.tpl', $assignData);
    }

    public function vlastnici2(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(38)) {
            return $this->response;
        };

        $assignData = ["page_title" => "Adminator3 :: Zákazníci 2"];

        $vlastnik2 = new \vlastnik2($this->container);
        $vlastnik2->adminator = $this->adminator;

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnik2->csrf_html = $csrf_html;

        // selectors form
        //
        $fs = new \App\Customer\fakturacniSkupiny($this->container);

        $select = $_GET["select"];

        if ($select == 2) {
            $fu_select = "2";
        } //Pouze FU
        if ($select == 3) {
            $fu_select = "1";
        } //pouze DU

        $assignData["select"] = $select;

        $assignData["fakt_skupiny"] = $fs->show_fakt_skupiny($fu_select);

        $assignData["fakt_skupiny_selected"] = $_GET['fakt_skupina'];

        $assignData["razeni"] = $_GET['razeni'];
        $assignData["razeni2"] = $_GET['razeni2'];

        if ($this->adminator->checkLevel(63) === true) {
            $assignData["vlastnici2_export_povolen"] = "true";
        }

        if ($this->adminator->checkLevel(40) === true) {
            $assignData["vlastnici2_pridani_povoleno"] = "true";
        }

        if (empty($_GET["find"])) {
            $vlastnik2->form_find = "%";
        } else {
            $vlastnik2->form_find = $this->conn_mysql->real_escape_string($_GET["find"]);
        }

        // main table
        $bodyContent = $vlastnik2->listItems();

        // $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": vlastnik2->listSql: " . var_export($vlastnik2->listSql, true));

        $assignData["form_search_value"] = preg_replace('/^(%)(.*)(%)$/', '\2', $vlastnik2->listSql);

        $assignData["body"] = $bodyContent;

        return $this->renderer->template($request, $response, 'vlastnici/vlastnici2.tpl', $assignData);
    }

    public function change(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(40)) {
            return $this->response;
        };

        $assignData = ["page_title" => "Adminator3 :: Zákazníci :: Update"];

        $assignData["enable_calendar2"] = 1;

        $vlastnik2 = new \vlastnici2pridani($this->container, $this->adminator);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $vlastnik2->csrf_html = $csrf_html;

        $assignData["body"] = $vlastnik2->action();

        $assignData["p_bs_alerts"] = $vlastnik2->p_bs_alerts;

        return $this->renderer->template($request, $response, 'vlastnici/change.tpl', $assignData);
    }

    public function archiv(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(82)) {
            return $this->response;
        };

        $assignData = ["page_title" => "Adminator3 :: Zákazníci :: Archiv"];

        $vlastnikArchiv = new \vlastnikarchiv();
        $vlastnikArchiv->conn_mysql = $this->conn_mysql;
        $vlastnikArchiv->conn_pgsql = $this->conn_pgsql;
        $vlastnikArchiv->logger = $this->logger;
        $vlastnikArchiv->container = $this->container;

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
            $assignData["form_find"] = "%";
        } else {
            $assignData["form_find"] = htmlspecialchars($find);
        }

        $assignData["form_select"] = $form_select;
        $assignData["form_razeni"] = $form_razeni;
        $assignData["form_razeni2"] = $form_razeni2;

        //promena pro update objektu
        if ($this->adminator->checkLevel(29) === true) {
            $vlastnikArchiv->objekt_update_povolen = true;
        }
        if ($this->adminator->checkLevel(33) === true) {
            $vlastnikArchiv->objekt_mazani_povoleno = true;
        }
        if ($this->adminator->checkLevel(34) === true) {
            $vlastnikArchiv->objekt_garant_akce = true;
        }

        // promeny pro mazani, zmenu vlastniku
        if ($this->adminator->checkLevel(45) === true) {
            $vlastnikArchiv->vlastnici_erase_povolen = true;
        }
        if ($this->adminator->checkLevel(30) === true) {
            $vlastnikArchiv->vlastnici_update_povolen = true;
        }

        // odendani objektu od vlastnika
        if ($this->adminator->checkLevel(49) === true) {
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
            $assignData["body"] = "Zadejte výraz k vyhledání.... <br>";

            return $this->renderer->template($request, $response, 'vlastnici/archiv.tpl', $assignData);
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

        $assignData["listing"] = $listovani->listInterval();

        $bodyContent .= $vlastnikArchiv->vypis($sql, $co, $dotaz_final);

        $bodyContent .= $vlastnikArchiv->vypis_tab(2);

        $assignData["body"] = $bodyContent;

        return $this->renderer->template($request, $response, 'vlastnici/archiv.tpl', $assignData);
    }

    public function fakturacniSkupiny(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(99)) {
            return $this->response;
        };

        $assignData = [
            "page_title" => "Adminator3 :: Zákazníci :: fakturační skupiny"
        ];

        // list logic
        //
        $fs = new \App\Customer\fakturacniSkupiny($this->container);
        $fs_items = $fs->getItems();

        if (empty($fs_items)) {
            $assignData["message_no_items"] = "Nebyly nalezeny žádné fakturační skupiny";
            return $this->renderer->template($request, $response, 'vlastnici/fakturacni-skupiny.tpl', $assignData);
        }

        $assignData["fs_items"] = $fs_items;

        return $this->renderer->template($request, $response, 'vlastnici/fakturacni-skupiny/list.tpl', $assignData);
    }

    public function fakturacniSkupinyAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->request = $request;
        $this->response = $response;

        if (!$this->checkLevel(301)) {
            return $this->response;
        };

        $fs = new \App\Customer\fakturacniSkupiny($this->container);
        $fs->csrf_html = $this->generateCsrfToken($request, $response, true);
        $fs->adminator = $this->adminator;

        $fs_action_body = $fs->Action();

        $assignData = array(
            "page_title" => "Adminator3 :: fakturační skupiny :: Action",
            "body" => $fs_action_body
        );

        return $this->renderer->template($request, $response, 'vlastnici/fakturacni-skupiny/action.tpl', $assignData);
    }

}
