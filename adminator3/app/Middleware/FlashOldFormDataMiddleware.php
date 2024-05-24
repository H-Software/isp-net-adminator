<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

/**
 * Class FlashOldFormData.
 */
class FlashOldFormDataMiddleware implements MiddlewareInterface
{
    /**
     * @var Messages
     */
    protected Messages $flash;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;    

    public function __construct(
        // Messages $flash,
        // LoggerInterface $logger
        ContainerInterface $container
    )
    {
        // $this->flash = $flash;
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
        $this->flash = $this->container->get('flash');
        if (!empty($params = $request->getParsedBody())) {
            $this->flash->addMessageNow('oldNow', $params);
            $this->flash->addMessage('old', $params);
        }

        return $handler->handle($request);
    }
}
