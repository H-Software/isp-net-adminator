<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Slim\Csrf\Guard;

class GuardMiddleware implements MiddlewareInterface
{
    /**
     * @var Guard
     */
    protected Guard $guard;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

     /**
     * @var \Smarty
     */
    protected \Smarty $smarty;

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    /**
     * @param ServerRequestInterface  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $feature = $this->container->get('openfeature');

        if($feature->getBooleanValue("adminator3SlimMiddlewareCsrf", true) === false) {
            $this->logger->warning(__CLASS__ . "\\" . __FUNCTION__ . ':  slim\csrf\guard middleware disabled over openFeature');
            return $handler->handle($request);
        }

        $this->guard = $this->container->get('csrf');
        $session_mw = $this->container->get(SessionMiddleware::class);
        $this->guard->setStorage($session_mw);

        $logger = $this->logger;
        // https://github.com/slimphp/Slim-Csrf?tab=readme-ov-file#handling-validation-failure
        $this->guard->setFailureHandler(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($logger) {
            $logger->error(
                __CLASS__ . "\\" . __FUNCTION__ . ": csrf check failed! ",
                array(
                    "uri" => $request->getUri(),
                    "headers" => var_export($request->getHeaders(), true)
                )
            );
            $request = $request->withAttribute("csrf_status", false);
            
            return $handler->handle($request);
        });

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ': calling slim\csrf\guard process');

        $response = $this->guard->process($request, $handler);

        if (false === $request->getAttribute('csrf_status')) {
            $logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": csrf_status false! rendering HTTP 500");

            // $this->smarty->assign("page_title", "Adminator3 - chybny level");

            // // $this->header($request, $response);
    
            // $this->smarty->assign("body", "<br>Neopravneny pristup /chyba pristupu. STOP <br>");
            // $this->smarty->display('global/no-level.tpl');
    
            // exit;
        }

        return $response;
    }

    private function FailureHandler()
    {

    }
}
