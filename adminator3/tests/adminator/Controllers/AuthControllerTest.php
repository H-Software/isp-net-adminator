<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\AuthController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class AuthControllerTest extends AdminatorTestCase
{
    protected function setUp(): void
    {
        // prepare data for forms
        //
        $_POST = array();
        $_GET = array();
        $_SERVER = array();
    }

    protected function tearDown(): void
    {
    }

    public function testLogin()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        $container = self::initDIcontainer();


    }
}
