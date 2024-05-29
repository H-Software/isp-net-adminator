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
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Csrf\Guard;

abstract class AdminatorTestCase extends TestCase
{
    public static $pdoMysql;

    public static $pdoPgsql;

    public static $capsule;

    public static $phinxConfig;

    public static $phinxManager;

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

    protected function runBasicAsserts($responseContent)
    {
        $this->assertNotEmpty($responseContent);

        $assertKeywordsCommon = array(
            '<html lang="en">',
            'link href="/public/css/style.css" rel="stylesheet" type="text/css" ',
            '<title>Adminator3',  // adminator head rendered
            'bootstrap.min.css" rel="stylesheet"',  // adminator head rendered
            'Jste přihlášeni v administračním systému', // adminator header rendered
            '<span class="intro-banner-logged"', // logged details container
            '<div id="obsah" >', // main container
            '<a class="cat" href="/vlastnici/cat" target="_top" >Zákazníci</a>', // categories - 1.line
            '<a class="cat" href="/partner/cat" target="_top" >Partner program</a>', // categories - 2.line
            '<div class="obsah-main" >', // inner container
            '</body>', // smarty rendered whole page
            '</html>' // smarty rendered whole page
        );

        foreach ($assertKeywordsCommon as $w) {

            $this->assertStringContainsString($w, $responseContent, __FUNCTION__ . " :: missing string \"" . $w . "\" in response body");

            // if (!str_contains($responseContent, $w)) {
            //     $this->assertFalse(true, "missing string \"" . $w . "\" in controller output");
            // }
        }

        $assertDeniedKeywordsCommon = [
            "failed",
            "error",
            "selhal",
            "nepodařil"
        ];

        // some words missing, because NoLoginPage and etc
        foreach ($assertDeniedKeywordsCommon as $w) {
            $this->assertStringNotContainsStringIgnoringCase($w, $responseContent, __FUNCTION__ . " :: found word (" . $w. "), which indicates error(s) or failure(s)");
        }

        // test sqlite migration
        // $sql = 'pragma table_info(\'board\');';
        // $sql2 = "SELECT * FROM board";
        // $rs = self::$pdoMysql->query($sql2);
        // print_r($rs->fetchAll());

        // debug
        // echo $responseContent;
    }
}
