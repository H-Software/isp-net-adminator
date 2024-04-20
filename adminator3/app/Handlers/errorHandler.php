<?php

namespace App\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Monolog\Logger;

// https://akrabat.com/logging-errors-in-slim-3/

// https://www.slimframework.com/docs/v3/handlers/error.html

final class Error extends \Slim\Handlers\Error
{

    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;

        $this->logger->info("handler\ErrorHandler construct called");
    }

    public function __invoke(Request $request, Response $response, $exception)
    {
        $this->logger->critical($exception->getMessage());

        return parent::__invoke($request, $response, $exception);
    }
}
