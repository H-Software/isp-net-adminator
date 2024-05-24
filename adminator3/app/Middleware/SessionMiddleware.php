<?php

declare(strict_types=1);

namespace App\Middleware;

use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param SessionInterface $session The session
     */
    public function __construct(
        SessionInterface $session,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->logger  = $logger;

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

        if (!$this->session->isStarted() && !headers_sent()) {
            $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . ": session not started, starting");
            $this->session->start();
        } elseif (!$this->session->isStarted()) {
            $this->logger->warning(__CLASS__ . "\\" . __FUNCTION__ . ": session not started, but headers already sent!");
        }

        if (!$this->session->has('regen') || $this->session->get('regen') < time()) {
            $this->session->regenerateId();
            $this->session->set('regen', time() + 300);
        }

        $this->logger->debug(__CLASS__ . "\\" . __FUNCTION__ . " iniciated");

        return $handler->handle($request);
    }
}
