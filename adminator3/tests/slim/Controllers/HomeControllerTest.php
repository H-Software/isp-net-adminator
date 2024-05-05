<?php

declare(strict_types=1);

namespace App\Tests;

use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use App\Controllers\HomeController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\CompiledContainer;
use DI\ContainerBuilder;
use EasyMock\EasyMock;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use PHPUnit\DbUnit\DataSet\MockDataSet;
use Psr\Http\Message\ResponseFactoryInterface;

class HomeControllerTest extends TestCase
{
    use EasyMock;
    use \phpmock\phpunit\PHPMock;

    protected function setUp(): void
    {
        global $pdoMysql;

        require __DIR__ . "/../../fixtures/bootstrapDatabase.php";

        $pdoMysql = $capsule->connection("default")->getPdo();

        $pdoConfig[ 'environments' ][ 'test' ] = [
            'adapter' => 'sqlite',
            'connection' => $pdoMysql,
            'table_prefix' => ''
        ];
        $pdoConfig["paths"] = [
            "migrations" => "database/migrations",
            'seeds'      => 'database/seeds',
        ];

        $config = new Config( $pdoConfig );
        $manager = new Manager( $config, new StringInput( ' ' ), new NullOutput() );
        $manager->migrate( 'test' );
        $manager->seed( 'test' );

        // require_once __DIR__ .'/../../fixtures/PDODbImporter.php';

        // $mysqlFile = __DIR__ .'/../../../../mysql-db-init-adminator2.sql';

        // $importerMysql = \PDODbImporter::importSQL($mysqlFile, $pdoMysql);

        $_POST = array();
        $_POST['show_se_cat'] = "null";

        $_GET = array();
        $_GET["v_reseni_filtr"] = 99;
        $_GET["vyreseno_filtr"] = 0;
        $_GET["limit"] = 10;

        $_SERVER = array();
        $_SERVER['HTTP_HOST'] = "127.0.0.1";
        $_SERVER['SCRIPT_URL'] = "/home";

        // function init_helper_base_html()
        // {
        //     return 1;
        // }

    }

    public function testHome()
    {
        // $this->markTestSkipped('under construction');
        // global $pdoMysql;

        $self = $this;

        // Make the ContainerBuilder use our fake class to catch constructor parameters
        // $builder = new ContainerBuilder(FakeContainer::class);

        // $otherContainer = $this->easyMock(ContainerInterface::class);

        // $container = new Container;

        //

        // $builder->wrapContainer($otherContainer);


        // /** @var FakeContainer $container */
        // $container = $builder->build();

        // $session_status = $this->getFunctionMock("Slim\Csrf", "session_status");
        // $session_status->expects($this->once())->willReturn(PHP_SESSION_ACTIVE);

        $builder = new ContainerBuilder();
        $builder->addDefinitions('tests/slim/fixtures/bootstrapContainer.php');
        $container = $builder->build();

        // $appMock = \Mockery::mock(
        //     \Slim\Factory\AppFactory::create(),
        //     [
        //         null,
        //         $container
        //     ]
        // );

        // $appMock->shouldReceive('getResponseFactory')->andReturn();

        // $responseFactory = $appMock->getResponseFactory();
        $rfMock = \Mockery::mock(ResponseFactoryInterface::class);
        $responseFactory = $rfMock;

        require_once __DIR__ . '/../fixtures/bootstrapContainerAfter.php';

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);

        $this->assertInstanceOf(ContainerInterface::class, $container);

        $this->assertInstanceOf(LoggerInterface::class, $container->get('logger'));
        $this->assertIsObject($container->get('smarty'));

        $adminatorMock = \Mockery::mock(
            \App\Core\adminator::class,
            [
                $container->get('connMysql'),
                $container->get('smarty'),
                $container->get('logger'),
                '127.0.0.1', // userIPAddress
            ]
        );

        $adminatorMock->userIdentityUsername = 'test@test';
        $adminatorMock->shouldReceive('checkLevel')->andReturn(true);
        $adminatorMock->shouldReceive('getServerUri')->andReturn("http://localhost:8080/home");
        $adminatorMock->shouldReceive('zobraz_kategorie')->andReturn(
            array(
                array(),
                array()
            )
        );
        $adminatorMock->shouldReceive('list_logged_users')->andReturn("");

        $adminatorMock->shouldReceive('show_stats_faktury_neuhr')->andReturn([0, 0, 0, 0]);

        $homeController = new HomeController($container, $adminatorMock);

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        ob_start();

        $homeController->home($serverRequest, $response, []);


        // test sqlite migration
        // $sql = 'pragma table_info(\'users\');';
        // $sql2 = "SELECT sql 
        // FROM sqlite_schema 
        // WHERE name = 'users';";
        // $rs = $pdoMysql->query($sql);
        // var_dump(print_r($rs->fetchAll()));

        $output = ob_get_contents();

        ob_end_clean();

        // debug
        echo $output;

        $this->assertNotEmpty($output);

        $outputKeywords = array(
            '<html lang="en">',
            '<title>Adminator3 :: úvodní stránka</title>',
            'bootstrap.min.css" rel="stylesheet"',
            'Jste přihlášeni v administračním systému',
            '<div class="home-vypis-useru-napis" >Přihlašení uživatelé: </div>',
            'Výpis Závad/oprav',
            'Bulletin Board - Nástěnka',
            '</body>',
            '</html>'
        );

        foreach ($outputKeywords as $w) {

            /*
            // N.B.: this causes printing output to stdout
            // maybe will works "assertThat()"
            // -> https://docs.phpunit.de/en/9.6/assertions.html#assertthat
            */
            // $this->assertStringContainsString($output, $w);

            if (!str_contains($output, $w)) {
                $this->assertFalse(true, "missing string \"" . $w . "\" in controller output");
            }
        }

        if (preg_match("/(failed|chyba|error)+/i", $output)) {
            // TODO: enable this assert after fix database UP operation
            // $this->assertFalse(true, "found some word(s), which indicates error(s)");
        }
    }
}
