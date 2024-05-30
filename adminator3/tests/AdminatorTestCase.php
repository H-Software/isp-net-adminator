<?php

// https://www.php.net/manual/en/regexp.reference.character-classes.php
// https://docs.phpunit.de/en/10.5/assertions.html#assertmatchesregularexpression

declare(strict_types=1);

namespace App\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use DI\CompiledContainer;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Csrf\Guard;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;
use Exception;
use Psr\Http\Message\ResponseInterface;

abstract class AdminatorTestCase extends TestCase
{
    public static $pdoMysql;

    public static $pdoPgsql;

    public static $capsule;

    public static $phinxConfig;

    public static $phinxManager;

    public static $psrHttpFactory;

    /*
    * code copied from laminas-test
    */

    /**
     * Trace error when exception is throwed in application
     *
     * @var bool
     */
    protected $traceError = true;

    /**
     * XPath namespaces
     *
     * @var array<string,string>
     */
    protected $xpathNamespaces = [];

    /*
    * end of code copied from laminas-test
    */

    public static function setUpBeforeClass(): void
    {
        $settings = require __DIR__ . '/../config/settings.php';

        // boot ORM and get DB handler
        require __DIR__ . "/fixtures/bootstrapDatabase.php";
        self::$pdoMysql = self::$capsule->connection("default")->getPdo();

        self::$pdoPgsql = self::$capsule->connection("pgsql")->getPdo();

        // override DB connection to sqlite
        $settings['phinx']['environments']['test']['connection'] = self::$pdoMysql;
        // setup seeds for Dev
        $settings['phinx']['paths'] = $settings['phinxDev']['paths'];

        // prepare DB structure and data
        self::$phinxConfig = new Config($settings['phinx']);
        self::$phinxManager = new Manager(self::$phinxConfig, new StringInput(' '), new NullOutput());
        self::$phinxManager->migrate('test');
        self::$phinxManager->seed('test');

        // factory for requests
        $psr17Factory = new Psr17Factory();

        // https://symfony.com/doc/current/components/psr7.html#converting-from-httpfoundation-objects-to-psr-7
        self::$psrHttpFactory = new PsrHttpFactory(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

    }

    public static function tearDownAfterClass(): void
    {
        self::$pdoMysql = null;
        self::$pdoPgsql = null;
        self::$capsule = null;

        self::$phinxConfig = null;
        self::$phinxManager = null;

        m::close();
    }

    protected function initDIcontainer(
        bool $sentinelMocked,
        bool $viewEnabled
    ) {
        $enableSession = false;

        // prepare DI
        $builder = new ContainerBuilder();
        $builder->addDefinitions('tests/fixtures/bootstrapContainer.php');
        $container = $builder->build();

        // $rfMock = m::mock(ResponseFactoryInterface::class);
        // $responseFactory = $rfMock;

        require __DIR__ . '/../tests/fixtures/bootstrapContainerAfter.php';

        if($sentinelMocked) {
            require __DIR__ . '/../tests/fixtures/containers/sentinelMock.php';
        } else {
            require __DIR__ . '/../tests/fixtures/containers/sentinel.php';
        }

        if($viewEnabled === true) {
            require __DIR__ . '/../tests/fixtures/containers/view.php';
            $enableSession = true;
        }

        // if($enableSession === true){
        //     $a = require 'tests/fixtures/containers/session.php';
        //     $container->set(key($a), $a[key($a)]);
        // }

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);

        $this->assertInstanceOf(ContainerInterface::class, $container);

        $this->assertInstanceOf(LoggerInterface::class, $container->get('logger'));

        // $this->assertIsObject($container->get('smarty'));
        $this->assertInstanceOf(\Smarty::class, $container->get('smarty'));

        $this->assertInstanceOf(\PDO::class, $container->get('connPgsql'));

        $this->assertInstanceOf(Guard::class, $container->get('csrf'));

        $this->assertInstanceOf(\Cartalyst\Sentinel\Sentinel::class, $container->get('sentinel'));

        $this->assertInstanceOf(\Slim\Flash\Messages::class, $container->get('flash'));

        return $container;
    }

    protected function initAdminatorMockClass(ContainerInterface $container, bool $mockCheckLevel = true, int $userIdentityLevel = 900)
    {
        // mock "underlaying" class for helper functions/logic
        $adminatorMock = m::mock(
            \App\Core\adminator::class,
            [
                $container->get('connMysql'),
                $container->get('smarty'),
                $container->get('logger'),
                '127.0.0.1', // userIPAddress
                $container->get('pdoMysql'),
                $container->get('settings'),
            ]
        )->makePartial();

        // probably useless, we have mocked sentinel object
        // $adminatorMock->userIdentityUsername = 'test@test';

        $adminatorMock->shouldReceive('getServerUri')->andReturn("http://localhost:8080/home");
        $adminatorMock->shouldReceive('getUserToken')->andReturn(false);
        // $adminatorMock->shouldReceive('show_stats_faktury_neuhr')->andReturn([0, 0, 0, 0]);

        if($mockCheckLevel) {
            $adminatorMock->shouldReceive('checkLevel')->andReturn(true);
        } else {
            // mock this, because we dont have data in database (probably)
            $adminatorMock->shouldReceive('getUserLevel')->andReturn($userIdentityLevel);
        }

        return $adminatorMock;
    }

    /*
    * code copied from laminas-test
    */

    /**
    * Create a failure message.
    *
    * If $traceError is true, appends exception details, if any.
    *
    * @deprecated (use LaminasContraint instead)
    *
    * @param string $message
    * @return string
    */
    // protected function createFailureMessage($message)
    // {
    //     if (! $this->traceError) {
    //         return $message;
    //     }

    //     $exception = $this->getApplication()->getMvcEvent()->getParam('exception');
    //     if (! $exception instanceof Throwable && ! $exception instanceof Exception) {
    //         return $message;
    //     }

    //     $messages = [];
    //     do {
    //         $messages[] = sprintf(
    //             "Exception '%s' with message '%s' in %s:%d",
    //             $exception::class,
    //             $exception->getMessage(),
    //             $exception->getFile(),
    //             $exception->getLine()
    //         );
    //     } while ($exception = $exception->getPrevious());

    //     return sprintf("%s\n\nExceptions raised:\n%s\n", $message, implode("\n\n", $messages));
    // }

    /**
    * Get the application response object
    *
    * @return Response
    */
    // public function getResponse()
    // {
    //     $response = $this->getApplication()->getMvcEvent()->getResponse();

    //     assert($response instanceof Response);

    //     return $response;
    // }

    /**
    * Execute a DOM/XPath query
    *
    * @param  string $path
    * @param  bool $useXpath
    * @return Crawler
    */
    private function query($response, $path, $useXpath = false)
    {
        $document = new Crawler($response->getBody()->__toString());

        if ($useXpath) {
            foreach ($this->xpathNamespaces as $prefix => $namespace) {
                $document->registerNamespace($prefix, $namespace);
            }
        }

        return $useXpath ? $document->filterXPath($path) : $document->filter($path);
    }

    /**
     * Execute a xpath query
     *
     * @param string $path
     */
    private function xpathQuery($response, $path): Crawler
    {
        return $this->query($response, $path, true);
    }

    /**
    * Count the dom query executed
    *
    * @param  string $path
    * @return int
    */
    private function queryCount($response, $path)
    {
        return count($this->query($response, $path, false));
    }

    /**
     * Count the dom query executed
     *
     * @param  string $path
     * @return int
     */
    private function xpathQueryCount($response, $path)
    {
        return $this->xpathQuery($response, $path)->count();
    }

    /**
     * @param string $path
     * @param bool $useXpath
     */
    private function queryCountOrxpathQueryCount($response, $path, $useXpath = false): int
    {
        if ($useXpath) {
            return $this->xpathQueryCount($response, $path);
        }

        return $this->queryCount($response, $path);
    }

    /**
     * Assert against DOM/XPath selection
     *
     * @param string $path
     * @param bool $useXpath
     */
    private function queryAssertion($response, $path, $useXpath = false): void
    {
        $match = $this->queryCountOrxpathQueryCount($response, $path, $useXpath);
        // if (! $match > 0) {
        //     throw new ExpectationFailedException($this->createFailureMessage(sprintf(
        //         'Failed asserting node DENOTED BY %s EXISTS',
        //         $path
        //     )));
        // }
        if (! $match > 0) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s EXISTS',
                $path
            ));
        }
        $this->assertTrue($match > 0);
    }

    /**
     * Assert against XPath selection
     *
     * @param string $path XPath path
     * @return void
     */
    public function assertXpathQuery($response, $path)
    {
        assert($response instanceof ResponseInterface);

        $this->queryAssertion($response, $path, true);
    }

    /**
     * Assert against DOM/XPath selection; node should contain content
     *
     * @param string $path CSS selector path
     * @param string $match content that should be contained in matched nodes
     * @param bool $useXpath
     */
    private function queryContentContainsAssertion($response, $path, $match, $useXpath = false): void
    {
        $result = $this->query($response, $path, $useXpath);

        if ($result->count() === 0) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s EXISTS',
                $path
            ));
        }

        $nodeValues = [];

        foreach ($result as $node) {
            if ($node->nodeValue === $match) {
                $this->assertEquals($match, $node->nodeValue);
                return;
            }

            $nodeValues[] = $node->nodeValue;
        }

        throw new ExpectationFailedException(sprintf(
            'Failed asserting node denoted by %s CONTAINS content "%s", Contents: [%s]',
            $path,
            $match,
            implode(',', $nodeValues)
        ));
    }

    /**
     * Assert against DOM/XPath selection; node should match content
     *
     * @param string $path CSS selector path
     * @param string $pattern Pattern that should be contained in matched nodes
     * @param bool $useXpath
     */
    private function queryContentRegexAssertion($response, $path, $pattern, $useXpath = false): void
    {
        $result = $this->query($response, $path, $useXpath);
        if ($result->count() === 0) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s EXISTS',
                $path
            ));
        }

        $found      = false;
        $nodeValues = [];

        foreach ($result as $node) {
            $nodeValues[] = $node->nodeValue;
            if (preg_match($pattern, (string) $node->nodeValue)) {
                $found = true;
                break;
            }
        }

        if (! $found) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node denoted by %s CONTAINS content MATCHING "%s", actual content is "%s"',
                $path,
                $pattern,
                implode('', $nodeValues)
            ));
        }

        $this->assertTrue($found);
    }

    /**
     * Assert against XPath selection; node should contain content
     *
     * @param string $path XPath path
     * @param string $match content that should be contained in matched nodes
     * @return void
     */
    public function assertXpathQueryContentContains($response, $path, $match)
    {
        $this->queryContentContainsAssertion($response, $path, $match, true);
    }

    /**
    * Assert against XPath selection; node should match content
    *
    * @param string $path XPath path
    * @param string $pattern Pattern that should be contained in matched nodes
    * @return void
    */
    public function assertXpathQueryContentRegex($response, $path, $pattern)
    {
        $this->queryContentRegexAssertion($response, $path, $pattern, true);
    }

    /*
    * end of code copied from laminas-test
    */

}
