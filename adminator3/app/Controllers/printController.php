<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Print\printClass;

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

            $soubory = $request->getParsedBody()['soubory'];
            $url = "/print/temp/" . htmlspecialchars($soubory);

            return $response
                ->withHeader('Location', $url)
                ->withStatus(302);

        } else {
            $response->getBody()->write("Error! Missing POST parameter.");
            $newResponse = $response->withStatus(500);
            return $newResponse;
        }
    }
}
