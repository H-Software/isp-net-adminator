<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Print\printClass;
use App\Print\printRegForm;

class printController extends adminatorController
{
    public $logger;

    protected $adminator;

    private $printInstance;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get('logger');
        $this->smarty = $this->container->get('smarty');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);

        $this->printInstance = new printClass($this->container);

    }

    public function printListAll(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(146, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Print");

        $this->header($request, $response, $this->adminator);

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $this->printInstance->csrf_html = $csrf_html;

        $this->printInstance->printListAll();

        return $response;
    }

    public function printRedirect(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(308, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Print :: redirect");

        $this->header($request, $response, $this->adminator);

        if ($request->getMethod() == "POST") {

            $fileName = $request->getParsedBody()['soubory'];
            list($fileName) = preg_match("/\/((\w|\-)\.pdf)/", $fileName);

            $content = $this->printInstance->getFileContent($fileName);

            if($content === false){
                $this->smarty->assign("alert_type", "danger");
                $this->smarty->assign("alert_content", "Error! Unable to get file (" . var_export($fileName,true) . ")");
                $this->smarty->display('print/redirect.tpl');

                $newResponse = $response->withStatus(500);
                return $newResponse;
            } else {
                $response = $response->withHeader('Content-type', 'application/pdf')
                ->withAddedHeader('Content-Description', 'File Transfer')
                ->withAddedHeader('Content-Disposition', 'attachment; filename=' . $fileName)
                ->withAddedHeader('Expires', '0')
                ->withAddedHeader('Cache-Control', 'must-revalidate')
                ->withAddedHeader('Pragma' ,'public');
                $response->getBody()->write($content);

                return $response;
            }
        } else {
            $this->smarty->assign("alert_type", "danger");
            $this->smarty->assign("alert_content", "Error! Missing POST parameter.");

            $this->smarty->display('print/redirect.tpl');

            $newResponse = $response->withStatus(500);
            return $newResponse;
        }
    }

    public function printRegForm201205New(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(146, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Ostatní :: Tisk - Reg. Form. 2012-05");

        $this->header($request, $response, $this->adminator);

        $rf = new printRegForm();

        $button_send = $_POST["send"];

        if(isset($button_send)) {

            //check a processing form
            $rf->load_input_vars();

            //generate pdf file
            $rf->generate_pdf_file();

            $this->smarty->assign("file_name", $rf->file_name);

            //finalni zobrazeni sablony
            $this->smarty->display('print/reg-form-2012-05.tpl');
        } else {

            //check a processing form
            $rf->load_input_vars();

            //zobrazeni formu a vyplneni hodnot
            $this->smarty->assign("form_action", "");

            $this->smarty->assign("input_ec", $rf->input_ec);

            list($csrf_html) = $this->generateCsrfToken($request, $response, true);
            $this->smarty->assign("csrf_html", $csrf_html);

            //finalni zobrazeni sablony
            $this->smarty->display('others/print-reg-form-2012-05-form.tpl');
        }

        return $response;
    }

    public function printRegForm201205Old(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(146, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Ostatní :: Tisk - Reg. Form. 2012-05");

        $this->header($request, $response, $this->adminator);

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $this->printInstance->csrf_html = $csrf_html;

        $this->printInstance->printRegForm201205Old();

        return $response;
    }

    public function printSmlouva201205(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(146, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Ostatní :: Tisk - Smlouva 2012-05");

        $this->header($request, $response, $this->adminator);

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $this->printInstance->csrf_html = $csrf_html;

        $this->printInstance->printSmlouva201205();

        return $response;
    }

    public function printSmlouva(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(146, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Ostatní :: Tisk - Smlouva");

        $this->header($request, $response, $this->adminator);

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $this->printInstance->csrf_html = $csrf_html;

        $this->printInstance->printSmlouva();

        return $response;
    }

    public function printRegForm(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(146, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Ostatní :: Tisk - Reg. Form");

        $this->header($request, $response, $this->adminator);

        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $this->printInstance->csrf_html = $csrf_html;

        $this->printInstance->regForm();

        return $response;
    }
}
