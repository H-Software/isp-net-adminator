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
    }

    public function printListAll(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(146, $this->adminator);

        $this->smarty->assign("page_title", "Adminator3 :: Print");

        $this->header($request, $response, $this->adminator);

        $this->printInstance = new printClass($this->container);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $this->printInstance->csrf_html = $csrf_html;

        $this->printInstance->printListAll();

        return $response;
    }

    public function printRedirect(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $this->checkLevel(308, $this->adminator);

        if ($request->getMethod() == "POST") {

            $fileName = $request->getParsedBody()['soubory'];
            $fullName = "print/temp/" . htmlspecialchars($fileName);

            // return $response
            //     ->withHeader('Location', $url)
            //     ->withStatus(302);

            $fh = fopen($fullName, "r");
            $content = fread($fh, filesize($fullName));
            fclose($fh);

            $response = $response->withHeader('Content-type', 'application/pdf')
                ->withAddedHeader('Content-Disposition', 'attachment; filename=' . $fileName);
            $response->getBody()->write($content);

            return $response;

        } else {
            $response->getBody()->write("Error! Missing POST parameter.");
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

        $this->printInstance = new printClass($this->container);
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

        $this->printInstance = new printClass($this->container);
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

        $this->printInstance = new printClass($this->container);
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

        $this->printInstance = new printClass($this->container);
        list($csrf_html) = $this->generateCsrfToken($request, $response, true);
        $this->printInstance->csrf_html = $csrf_html;

        $this->printInstance->regForm();

        return $response;
    }
}
