<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class adminatorController extends Controller
{
    public $conn_mysql;
    public $smarty;
    public $logger;

    private $sentinel;

    public function __construct($conn_mysql, $smarty, $logger, $sentinel)
    {
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        $this->sentinel = $sentinel;

        $this->logger->info("adminatorController\__construct called");
    }

    public function jsonRender(ServerRequestInterface $request, ResponseInterface $response, $data, $status = 200, $msg = '')
    {

        // $this->logger->info("JsonViewer\\render called");
        // $this->logger->info("JsonViewer\\render response dump: " . var_export($data, true));

        $status = intval($status);
        $_response = ['code' => $status, 'data' => null, 'msg' => ''];
        if(200 == $status or 418 == $status) {
            $_response['data'] = $data;
        } else {
            $_response['msg'] = $msg;
        }

        // TODO: fix unknown withJson
        // $newResponse = $response->withJson($_response, $status, JSON_PRETTY_PRINT);
        // // $this->logger->info("JsonViewer\\render response dump: " . var_export($newResponse, true));

        // return $newResponse;
        
        return $response;
    }

    // public function jsonRenderException($status = 0, $msg = '') {
    //     $status = intval($status);
    //     $response = ['code' => $status, 'data' => null, 'msg' => $msg];
    //     $this->_response->withJson($response);
    // }

    public function renderNoLogin(ServerRequestInterface $request = null, ResponseInterface $response = null)
    {
        $this->smarty->assign("page_title", "Adminator3 - chybny level");

        $this->header($request, $response);

        $this->smarty->assign("body", "<br>Neopravneny pristup /chyba pristupu. STOP <br>");
        $this->smarty->display('global/no-level.tpl');

        exit;
    }
    public function checkLevel($page_level_id = 0, $adminator = null): void
    {

        if(is_object($adminator)) {
            $a = $adminator;
        } else {
            $a = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
        }

        if ($page_level_id == 0) {
            $this->renderNoLogin();
            // return false;
        }

        $a->page_level_id = $page_level_id;

        if(strlen($a->userIdentityUsername) < 1 or $a->userIdentityUsername == null) {
            if(Sentinel::getUser()->email == null){
                $this->logger->error("adminatorController\checkLevel: getUser from sentinel failed");
                $this->renderNoLogin();
                // return false;
            } else{
                $a->userIdentityUsername = Sentinel::getUser()->email;
            }
        }

        $this->logger->debug("adminatorController\checkLevel: current identity: ".var_export($a->userIdentityUsername, true));

        $checkLevel = $a->checkLevel();

        $this->logger->info("adminatorController\checkLevel: checkLevel result: ".var_export($checkLevel, true));

        if($checkLevel === false) {
            $this->renderNoLogin();
            // return false;
        }
    }

    public function generateCsrfToken(ServerRequestInterface $request, ResponseInterface $response, $return_form_html = false)
    {

        $ret = array();

        // CSRF token name and value for update form
        $csrf = $this->container->get('csrf');
        $csrf_nameKey = $csrf->getTokenNameKey();
        $csrf_valueKey = $csrf->getTokenValueKey();
        $csrf_name = $request->getAttribute($csrf_nameKey);
        $csrf_value = $request->getAttribute($csrf_valueKey);

        if($return_form_html === true) {
            $ret[0] = '<input type="hidden" name="'.$csrf_nameKey.'" value="'.$csrf_name.'">'
                       . '<input type="hidden" name="'.$csrf_valueKey.'" value="'.$csrf_value.'">';
        } else {
            $ret = array("", $csrf_nameKey, $csrf_valueKey, $csrf_name, $csrf_value);
        }

        return $ret;
    }

    public function header(ServerRequestInterface|null $request, ResponseInterface|null $response, $adminator = null)
    {

        if(is_object($adminator)) {
            $a = $adminator;
        } else {
            $a = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);
        }

        $this->logger->debug("adminatorController\\header called");
        $this->logger->debug("adminatorController\\header: logged user info: " . $a->userIdentityUsername . " (" . $a->userIdentityLevel . ")");

        $this->smarty->assign("nick_a_level", $a->userIdentityUsername . " (" . $a->userIdentityLevel . ")");
        $this->smarty->assign("login_ip", $a->userIPAddress);

        //kategorie

        $uri = $a->getServerUri();
        $uri_replace = str_replace("adminator3", "", $uri);

        list($kategorie, $kat_2radka) = $a->zobraz_kategorie($uri, $uri_replace);

        $this->smarty->assign("kategorie", $kategorie);
        $this->smarty->assign("kat_2radka", $kat_2radka);

        // $uri = $request->getUri();
        // $current_url = $uri->getPath(); // . "?" . $uri->getQuery();

        // $this->smarty->assign("se_cat_form_action", $current_url);

        if(is_object($request) and is_object($response)) {
            list($csrf_html) = $this->generateCsrfToken($request, $response, true);
            // $this->logger->info("adminController\header: csrf generated: ".var_export($csrf, true));
            $this->smarty->assign("kat_csrf_html", $csrf_html);
        } else {
            $this->logger->warning("adminatorController\\header: no required vars for generateCsrfToken");
        }

        $this->smarty->assign("show_se_cat_values", array("0","1"));
        $this->smarty->assign("show_se_cat_output", array("Nezobr. odkazy","Zobrazit odkazy"));

        // $show_se_cat = $_POST["show_se_cat"];
        $show_se_cat = 0;
        if($$request != null){
            if ($request->getMethod() == "POST") {
                $show_se_cat = $request->getParsedBody()['show_se_cat'];
                $this->logger->debug("adminatorController\\header: parsed show_se_cat with: ".var_export($show_se_cat, true));
            } 
        }

        $this->smarty->assign("show_se_cat_selected", $show_se_cat);

        // $this->logger->debug("adminatorController\\header: show_se_cat value: ".$show_se_cat);

        $this->smarty->assign("show_se_cat", $show_se_cat);

        $se_cat_adminator_link = $_SERVER['HTTP_HOST'];
        $se_cat_adminator_link = str_replace("adminator3", "adminator2", $se_cat_adminator_link);
        if (isset($_SERVER['HTTPS'])) {
            $se_cat_adminator_link = "https://" . $se_cat_adminator_link;
        } else {
            $se_cat_adminator_link = "http://" . $se_cat_adminator_link;
        }

        $this->smarty->assign("se_cat_adminator", "adminator2");
        $this->smarty->assign("se_cat_adminator_link", $se_cat_adminator_link);
    }
}
