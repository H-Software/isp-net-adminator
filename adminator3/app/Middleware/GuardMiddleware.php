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

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
        $this->logger = $container->get('logger');
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

        $this->guard = $this->container->get('csrf');
        $feature = $this->container->get('openfeature');
        $session_mw = $this->container->get(SessionMiddleware::class);

        $this->guard->setStorage($session_mw);

        if($feature->getBooleanValue("adminator3SlimMiddlewareCsrf", true)) {
            $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ': calling slim\csrf\guard');

            $response = $this->guard->process($request, $handler);
        } else {
            $this->logger->warning(__CLASS__ . "\\" . __FUNCTION__ . ':  slim\csrf\guard middleware disabled over openFeature');
            $response = $handler->handle($request);
        }
        
        return $response;
    }
}
