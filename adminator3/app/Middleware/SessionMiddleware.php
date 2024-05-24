<?php

declare(strict_types=1);

namespace App\Middleware;

use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Csrf\Guard;

/**
 * Class SessionMiddleware.
 */
class SessionMiddleware implements MiddlewareInterface
{
    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    /**
     * @var Guard
     */
    protected Guard $guard;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param SessionInterface $session The session
     */
    public function __construct(
        SessionInterface $session,
        Guard $guard,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->guard   = $guard;
        $this->logger  = $logger;
    }

    /**
     * @param ServerRequestInterface  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->debug("SessionMiddleware called");

        if (!$this->session->isStarted() && !headers_sent()) {
            $this->logger->debug("SessionMiddleware: session not started, starting");
            $this->session->start();
        } elseif (!$this->session->isStarted()) {
            $this->logger->warning("SessionMiddleware: session not started, but headers already sent!");
        }

        if (!$this->session->has('regen') || $this->session->get('regen') < time()) {
            $this->session->regenerateId();
            $this->session->set('regen', time() + 300);
        }

        // $this->guard->setStorage($this);

        $this->logger->debug("SessionMiddleware iniciated");

        return $handler->handle($request);
    }
}
