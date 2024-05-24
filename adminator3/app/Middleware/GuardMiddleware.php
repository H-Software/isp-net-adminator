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

    private function initGuard(): void
    {
        $this->guard = $this->container->get('csrf');
        $session_mw = $this->container->get(SessionMiddleware::class);
        $this->guard->setStorage($session_mw);
    }

    private function SetFailureHandler(): void
    {
        $logger = $this->logger;
        $smarty = $this->smarty;

        // https://github.com/slimphp/Slim-Csrf?tab=readme-ov-file#handling-validation-failure
        $this->guard->setFailureHandler(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($logger, $smarty) {

            $logger->error(
                __CLASS__ . "\\" . __FUNCTION__ . ": csrf check failed! ",
                array(
                    "uri" => $request->getUri(),
                    "headers" => var_export($request->getHeaders(), true)
                )
            );
            // $request = $request->withAttribute("csrf_status", false);
            // $this->smarty->assign("page_title", "Adminator3 - chybny CSRF token");

            // // $this->header($request, $response);
    
            // $this->smarty->assign("body", "<br>Neopravneny pristup /chyba pristupu. STOP <br>");
            // $this->smarty->display('global/no-level.tpl');
    
            // $response = $this->responseFactory->createResponse();
            // $body = $response->getBody();
            // $body->write('Failed CSRF check!');
            // return $response
            //     ->withStatus(400)
            //     ->withHeader('Content-Type', 'text/plain')
            //     ->withBody($body);
            // exit;
            
            return $handler->handle($request);
        });
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

        $this->initGuard();

        $this->SetFailureHandler();

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ': calling slim\csrf\guard process');

        $response = $this->guard->process($request, $handler);

        return $response;
    }
}
