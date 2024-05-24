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

        $feature = $this->container->get('openfeature');

        if($feature->getBooleanValue("adminator3SlimMiddlewareCsrf", true) === false) {
            $this->logger->warning(__CLASS__ . "\\" . __FUNCTION__ . ':  slim\csrf\guard middleware disabled over openFeature');
            return $handler->handle($request);
        }

        $this->guard = $this->container->get('csrf');
        $session_mw = $this->container->get(SessionMiddleware::class);
        $this->guard->setStorage($session_mw);

        $smarty = $this->container->get('smarty');
        $logger = $this->logger;

        // TODO: set custom failureHandler
        // https://akrabat.com/slim-csrf-with-slim-3/#customising-the-csrf-failure
        // https://github.com/adbario/slim-csrf?tab=readme-ov-file#custom-error-on-csrf-token-failure
        $this->guard->setFailureHandler(function ($request, $response, $next) use ($logger) {
            $logger->error(__CLASS__ . "\\" . __FUNCTION__ . ": csrf check failed! ");

            // $response = $responseFactory->createResponse();
            $body = $response->getBody();
            $body->write('Failed CSRF check!');
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'text/plain')
                ->withBody($body);

            return $next($request, $response);
        });

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ': calling slim\csrf\guard process');

        $response = $this->guard->process($request, $handler);

        return $response;
    }
}
