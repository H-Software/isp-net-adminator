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
        // $this->logger->info("handler\ErrorHandler construct called");
    }

    public function __invoke(Request $request, Response $response, $exception)
    {
        $exMessage   = $exception->getMessage();
        $logMessage  = "Exception Message: $exMessage";
        $this->logger->critical($logMessage, [ "exception" => $exception ]);

        // https://www.php.net/manual/en/exception.gettraceasstring.php#114980
        // https://codereview.stackexchange.com/questions/145239/prettifying-phps-exception-stacktraces
        // $this->logger->debug("Exception trace: " . $exception->getTraceAsString());

        return parent::__invoke($request, $response, $exception);
    }
}
