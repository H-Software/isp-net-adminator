<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
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
     * @var ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
    * @var \Smarty
    */
    protected \Smarty $smarty;

    public function __construct(
        ContainerInterface $container,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->container = $container;
        $this->responseFactory = $responseFactory;

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
        $responseFactory = $this->responseFactory;

        // https://github.com/slimphp/Slim-Csrf?tab=readme-ov-file#handling-validation-failure
        $this->guard->setFailureHandler(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($logger, $smarty, $responseFactory) {

            $logger->error(
                __CLASS__ . "\\" . __FUNCTION__ . ": csrf check failed! ",
                array(
                    "uri" => $request->getUri(),
                    "headers" => var_export($request->getHeaders(), true)
                )
            );

            $response = $responseFactory->createResponse()
                        ->withStatus(400)
                        ->withHeader('Content-Type', 'text/plain');

            $request = $request->withAttribute("csrf_status", false);
            $smarty->assign("page_title", "Adminator3 - chybny CSRF token");

            // // $this->header($request, $response);

            $smarty->assign("body", "<br>Failed CSRF check!<br>");
            $body = $smarty->fetch('global/no-csrf.tpl');

            return $response;

            // return $handler->handle($request);
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
