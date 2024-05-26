<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
// use Psr\Http\Message\ResponseFactoryInterface;
use Exception;

class adminatorController extends Controller
{
    public $conn_mysql;

    public $conn_pgsql;

    public $smarty;

    public $logger;

    public $pdoMysql;

    public $settings;

    protected $sentinel;

    protected $adminator;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    // /**
    //  * @var ResponseFactoryInterface
    //  */
    // protected ResponseFactoryInterface $responseFactory;

    public function __construct($container)
    {
        $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');

        $this->smarty = $container->get('smarty');
        $this->logger = $container->get('logger');
        $this->sentinel = $container->get('sentinel');
        $this->pdoMysql = $this->container->get('pdoMysql');
        $this->settings = $this->container->get('settings');

        // $this->responseFactory = $container->get(ResponseFactoryInterface::class);

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        // for using mocked instance in tests
        if(isset($adminatorInstance)) {
            $this->adminator = $adminatorInstance;
        } else {
            $this->adminator = new \App\Core\adminator(
                $this->conn_mysql,
                $this->smarty,
                $this->logger,
                null,
                $this->pdoMysql,
                $this->settings,
                $this->conn_pgsql
            );
        }

        // moved this into constructor for using identity across whole application
        if(strlen($this->adminator->userIdentityUsername) < 1 or $this->adminator->userIdentityUsername == null) {
            if($this->sentinel->getUser()->email == null) {
                $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": getUser from sentinel failed");
                throw new Exception("Call " . __CLASS__ . "\\" . __FUNCTION__ . " failed: cannot get user identity! (getUser from sentinel)");
            } else {
                $this->adminator->userIdentityUsername = $this->sentinel->getUser()->email;
            }
        }

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": current identity: ".var_export($this->adminator->userIdentityUsername, true));
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

    public function createNoLoginResponse($content): ResponseInterface
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403
        $this->response = $this->response
                            ->withStatus(403);

        $this->response->getBody()->write($content);

        return $this->response;
    }

    public function renderNoLogin(): string
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->smarty->assign("page_title", "Adminator3 :: wrong level");

        $this->header($this->request, $this->response);

        $this->smarty->assign("body", "<br>Neopravneny pristup /chyba pristupu. STOP <br>");
        $content = $this->smarty->fetch('global/no-level.tpl');

        return $content;
    }

    public function checkLevel($page_level_id = 0): bool
    {
        // wrapper for checking user's level vs. page level
        // core function for checking level is in adminator class and shared with adminator2
        // and accessible directly without needs of controller scope

        // check input var(s)
        if ($page_level_id == 0) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": page_level_id == 0");
            throw new Exception("Call " . __CLASS__ . "\\" . __FUNCTION__ . " failed: page_level_id is 0.");
        }

        // "double" check for some backwards compatibility shit
        if(!is_object($this->adminator)) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": instance of Adminator class not exists");
            throw new Exception("Call " . __CLASS__ . "\\" . __FUNCTION__ . " failed: cannot verify user login.");
        }

        $this->adminator->page_level_id = $page_level_id;

        // TODO: after fix calling adminatorController constructor in every other controller, remove this
        // if(strlen($this->adminator->userIdentityUsername) < 1 or $this->adminator->userIdentityUsername == null) {
        //     if($this->sentinel->getUser()->email == null) {
        //         $this->logger->error("adminatorController\checkLevel: getUser from sentinel failed");
        //         $content = $this->renderNoLogin();
        //         $this->createNoLoginResponse($content);
        //         return false;
        //     } else {
        //         $this->adminator->userIdentityUsername = $this->sentinel->getUser()->email;
        //     }
        // }

        // double check identity
        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": current identity: ".var_export($this->adminator->userIdentityUsername, true));

        // real check
        $checkLevel = $this->adminator->checkLevel();
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": checkLevel result: ".var_export($checkLevel, true));

        if($checkLevel === false) {
            $content = $this->renderNoLogin();
            $this->createNoLoginResponse($content);
            return false;
        }

        return true;
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

    public function header(ServerRequestInterface|null $request, ResponseInterface|null $response, $adminatorUnsed = null)
    {
        $this->logger->debug("adminatorController\\header called");
        $this->logger->debug("adminatorController\\header: logged user info: " . $this->adminator->userIdentityUsername . " (" . $this->adminator->userIdentityLevel . ")");

        $this->smarty->assign("nick_a_level", $this->adminator->userIdentityUsername . " (" . $this->adminator->userIdentityLevel . ")");
        $this->smarty->assign("login_ip", $this->adminator->userIPAddress);

        //kategorie
        $uri = $this->adminator->getServerUri();
        $uri_replace = str_replace("adminator3", "", $uri);

        list($kategorie, $kat_2radka) = $this->adminator->zobraz_kategorie($uri, $uri_replace);

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
        if($request != null) {
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
