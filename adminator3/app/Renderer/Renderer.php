<?php

namespace App\Renderer;

use OpenTelemetry\SDK\Resource\Detectors\Sdk;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Csrf\Guard;
use Symfony\Component\HttpFoundation\Request;

final class Renderer
{
    /**
    * @var ContainerInterface
    */
    protected ContainerInterface $container;

    /**
    * @var LoggerInterface
    */
    protected LoggerInterface $logger;

    /**
    * @var \Smarty
    */
    protected \Smarty $smarty;

    /**
    * @var Guard
    */
    protected Guard $csrf;

    public ?string $userIdentityUsername = null;

    public ?int $userIdentityLevel = null;

    private \Symfony\Component\HttpFoundation\Request $requestData;

    public function __construct(
        ContainerInterface $container,
    ) {
        $this->container = $container;
        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        $this->requestData = Request::createFromGlobals();
    }

    public function template(
        ?ServerRequestInterface $request,
        ResponseInterface $response,
        string $template,
        array $assignData = [],
        int $httpStatusCode = 200
    ): ResponseInterface {
        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . " called");

        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
        $response = $response
                        ->withStatus($httpStatusCode);

        $this->header($request, $response);

        foreach ($assignData as $name => $value) {
            $this->smarty->assign($name, $value);
        }

        try {
            $content = $this->smarty->fetch($template);
        } catch (\SmartyException $e) {
            $this->logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": caught SmartyException: " . var_export($e->getMessage(), true));

            $this->smarty->assign("page_title", "Renderer Error");
            $this->smarty->assign("body", $e->getMessage());
            $content = $this->smarty->fetch("global/smarty-exception.tpl");

            $response = $response
                            ->withStatus(500);
        }

        $response->getBody()->write($content);

        return $response;
    }

    public function header(ServerRequestInterface|null $request, ResponseInterface $response): void
    {
        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->getIdentityForHeader();

        //kategorie
        $uri = \App\Core\adminator::getServerUri();

        list($kategorie, $kat_2radka) = $this->zobraz_kategorie($uri);

        $this->smarty->assign("kategorie", $kategorie);
        $this->smarty->assign("kat_2radka", $kat_2radka);

        if(is_object($request) and is_object($response)) {
            list($csrf_html) = $this->generateCsrfToken($request, $response, true, $this->container->get('csrf'));
            // $this->logger->info("adminController\header: csrf generated: ".var_export($csrf, true));
            $this->smarty->assign("kat_csrf_html", $csrf_html);
        } else {
            $this->logger->warning(__CLASS__ . "\\" . __FUNCTION__ . ": no required objects for generateCsrfToken");
        }

        // logic for showing extra line of stuff (SEcondary CATegories)
        $show_se_cat = 0;
        if($request != null) {
            if ($request->getMethod() == "POST") {
                $show_se_cat = $request->getParsedBody()['show_se_cat'];
                $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": parsed show_se_cat with: ".var_export($show_se_cat, true));
            }

            $this->smarty->assign("show_se_cat_values", array("0","1"));
            $this->smarty->assign("show_se_cat_output", array("Nezobr. odkazy","Zobrazit odkazy"));

            $this->smarty->assign("show_se_cat_selected", $show_se_cat);

            // $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": show_se_cat value: ".$show_se_cat);

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
        } else {
            $this->smarty->assign("show_se_cat_selector_disable", 1);
        }
    }

    private function getIdentityForHeader(): void
    {
        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": current identity: " . $this->userIdentityUsername . " (" . $this->userIdentityLevel . ")");

        $this->smarty->assign("nick_a_level", $this->userIdentityUsername . " (" . $this->userIdentityLevel . ")");
        $this->smarty->assign("login_ip", $this->requestData->server->get('REMOTE_ADDR'));
    }

    public static function zobraz_kategorie(string $uri): array
    {
        $kategorie = array();

        $kategorie[0] = array( "nazev" => "Zákazníci", "url" => "/vlastnici/cat", "align" => "center", "width" => "18%" );

        if(preg_match("/^\/vlastnici.*/", $uri) or preg_match("/^\/vypovedi.*/", $uri)) {
            $kategorie[0]["barva"] = "silver";
        }

        $kategorie[1] = array( "nazev" => "Služby", "url" => "/objekty/cat", "align" => "center", "width" => "18%" );

        if(preg_match("/^\/objekty.*/", $uri)) {
            $kategorie[1]["barva"] = "silver";
        }

        $kategorie[2] = array( "nazev" => "Platby", "url" => "/platby/cat", "align" => "center", "width" => "18%" );

        // if( ereg("^.+platby.+$",$uri) )
        // { $kategorie[2]["barva"] = "silver"; }

        $kategorie[3] = array( "nazev" => "Topologie", "url" => "/topology", "align" => "center", "width" => "" );

        // if( ereg("^.+topology",$uri) )
        // { $kategorie[3]["barva"] = "silver"; }

        $kategorie[4] = array( "nazev" => "Nastavení", "url" => "/admin", "align" => "center", "width" => "" );

        // if( ereg("^.+admin.+$",$uri_replace ) )
        // {  $kategorie[4]["barva"] = "silver"; }

        $kategorie[5] = array( "nazev" => "Úvodní strana", "url" => "/home", "align" => "center", "width" => "" );

        // if( ereg("^.+home.php$",$uri) )
        // { $kategorie[5]["barva"] = "silver"; }

        $kat_2radka = array();

        $kat_2radka[0] = array( "nazev" => "Partner program", "url" => "/partner/cat", "width" => "", "align" => "center" );

        // if( (ereg("partner",$uri_replace) and !ereg("admin",$uri_replace)) )
        // { $kat_2radka[0]["barva"] = "silver"; }

        $kat_2radka[1] = array( "nazev" => "Změny", "url" => "/archiv-zmen/cat", "width" => "", "align" => "center" );

        // if( ereg("^.+archiv-zmen.+$",$uri) )
        // { $kat_2radka[1]["barva"] = "silver"; }

        $kat_2radka[2] = array( "nazev" => "Work", "url" => "/work", "width" => "", "align" => "center" );

        // if( ereg("^.+work.+$",$uri) )
        // { $kat_2radka[2]["barva"] = "silver"; }

        $kat_2radka[3] = array( "nazev" => "Ostatní", "url" => "/others", "width" => "", "align" => "center" );

        // if( ereg("^.+others.+$",$uri) or ereg("^.+syslog.+$",$uri) or ereg("^.+/mail.php$",$uri) or ereg("^.+opravy.+$",$uri) )
        // { $kat_2radka[3]["barva"] = "silver"; }

        $kat_2radka[4] = array( "nazev" => "O programu", "url" => "/about", "width" => "", "align" => "center" );

        // if( ereg("^.+about.+$",$uri) )
        // { $kat_2radka[4]["barva"] = "silver"; }

        $ret = array( $kategorie, $kat_2radka);

        return $ret;
    }

    public static function generateCsrfToken(ServerRequestInterface $request, ResponseInterface $response, $return_form_html, $csrf)
    {
        $ret = array();

        // CSRF token name and value for update form
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

}
