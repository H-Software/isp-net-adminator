<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class adminatorController extends Controller {

    var $conn_mysql;
    var $smarty;
    var $logger;
    var $auth;
    
    public function __construct($conn_mysql, $smarty, $logger)
    {
		$this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        // $this->auth = $auth;
        
        $this->logger->addInfo("adminatorController\__construct called");
	}

    public function jsonRender(ServerRequestInterface $request, ResponseInterface $response, $data, $status = 200, $msg = '') {
        
        // $this->logger->addInfo("JsonViewer\\render called");
        // $this->logger->addInfo("JsonViewer\\render response dump: " . var_export($data, true));

        $status = intval($status);
        $_response = ['code' => $status, 'data' => null, 'msg' => ''];
        if(200 == $status or 418 == $status){
            $_response['data'] = $data;
        }else{
            $_response['msg'] = $msg;
        }

        $newResponse = $response->withJson($_response, $status, JSON_PRETTY_PRINT);
        // $this->logger->addInfo("JsonViewer\\render response dump: " . var_export($newResponse, true));

        return $newResponse;
    }

    // public function jsonRenderException($status = 0, $msg = '') {
    //     $status = intval($status);
    //     $response = ['code' => $status, 'data' => null, 'msg' => $msg];
    //     $this->_response->withJson($response);
    // }
    
    public function renderNoLogin ()
    {
        $this->smarty->assign("page_title","Adminator3 - chybny level");

        $this->header();

        $this->smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br>");
        $this->smarty->display('global/no-level.tpl');
        
        exit;
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
        // $this->logger->addInfo("adminatorController\\check_level getIdentity: ".var_export( $auth_identity['username'], true));

        if ($page_level_id == 0){
            $this->renderNoLogin();
            return false;
        }

        $a->page_level_id = $page_level_id;
        $a->userIdentityUsername = $auth_identity['username'];

        $checkLevel = $a->checkLevel();
        
        $this->logger->addInfo("adminatorController\checkLevel: checkLevel result: ".var_export($checkLevel, true));

        if($checkLevel === false){
            $this->renderNoLogin();
            return false;
        }
    }

    public function generateCsrfToken(ServerRequestInterface $request, ResponseInterface $response, $return_form_html = false){
        
            $ret = array();

            // CSRF token name and value for update form
            // TODO: fix after 8.1 switch
            // $csrf = $this->container->get('csrf');
            // $csrf_nameKey = $csrf->getTokenNameKey();
            // $csrf_valueKey = $csrf->getTokenValueKey();
            // $csrf_name = $request->getAttribute($csrf_nameKey);
            // $csrf_value = $request->getAttribute($csrf_valueKey);

            if($return_form_html === true)
            {
                $ret[0] = '<input type="hidden" name="'.$csrf_nameKey.'" value="'.$csrf_name.'">'
                           . '<input type="hidden" name="'.$csrf_valueKey.'" value="'.$csrf_value.'">';
            }
            else
            {
                $ret = array("", $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value);
            }

            return $ret;
    }

    function header(ServerRequestInterface $request = null, ResponseInterface $response = null, $adminator = null)
    {

        if(is_object($adminator))
        {
            $a = $adminator;
        }
        else
        {
            $a = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
        }

        $this->logger->addDebug("adminatorController\\header called");
        $this->logger->addDebug("adminatorController\\header: logged user info: " . $a->userIdentityUsername . " (" . $a->userIdentityLevel . ")");

        $this->smarty->assign("nick_a_level", $a->userIdentityUsername . " (" . $a->userIdentityLevel . ")");
        $this->smarty->assign("login_ip",$_SERVER['REMOTE_ADDR']);

        //kategorie

        $uri=$_SERVER["REQUEST_URI"];
        $uri_replace = str_replace ("adminator3", "", $uri);

        list($kategorie, $kat_2radka, $mapa) = $a->zobraz_kategorie($uri,$uri_replace);

        $this->smarty->assign("kategorie",$kategorie);
        $this->smarty->assign("kat_2radka",$kat_2radka);

        if(is_object($request) and is_object($response)){
            $csrf = $this->generateCsrfToken($request, $response, true);
            // $this->logger->addInfo("adminController\header: csrf generated: ".var_export($csrf, true));
            $this->smarty->assign("kat_csrf_html", $csrf[0]);
        }
        else
        {
            $this->logger->addWarning("adminatorController\\header: no required vars for generateCsrfToken");
        }

        $this->smarty->assign("show_se_cat_values", array("0","1"));
        $this->smarty->assign("show_se_cat_output", array("Nezobr. odkazy","Zobrazit odkazy"));

        $show_se_cat = $_POST["show_se_cat"];

        if( $show_se_cat == 0 )
        { $this->smarty->assign("show_se_cat_selected", "0"); }
        else
        { $this->smarty->assign("show_se_cat_selected", "1"); }

        $this->smarty->assign("show_se_cat",$show_se_cat);

        $se_cat_adminator_link = $_SERVER['HTTP_HOST'];
        $se_cat_adminator_link = str_replace("adminator3", "adminator2", $se_cat_adminator_link);

        $this->smarty->assign("se_cat_adminator","adminator2");
        $this->smarty->assign("se_cat_adminator_link",$se_cat_adminator_link);

        // $prihl_uziv = $a->vypis_prihlasene_uziv();

        // if( $prihl_uziv[100] == true ){
        //     $this->smarty->assign("pocet_prihl_uziv",0);
        // }
        // else{
        //     $this->smarty->assign("pocet_prihl_uziv",$prihl_uziv[0]);

        //     $this->smarty->assign("prvni_jmeno",$prihl_uziv[1]);
        //     $this->smarty->assign("prvni_level",$prihl_uziv[2]);
        // }

        //button na vypis vsech prihl. uziv.
        // $this->smarty->assign("windowtext2",$prihl_uziv[3]);

        // // velikost okna
        // $this->smarty->assign("windowdelka2","170");
        // $this->smarty->assign("windowpadding2","40");
            
        // // pozice okna
        // $this->smarty->assign("windowtop2","150px");
        // $this->smarty->assign("windowleft2","50%");

        // $this->smarty->assign("subcat_select",0);
    }
}
