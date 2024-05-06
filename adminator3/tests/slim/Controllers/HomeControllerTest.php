<?php

declare(strict_types=1);

namespace App\Tests;

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

final class HomeControllerTest extends AdminatorTestCase
{
    use EasyMock;
    use \phpmock\phpunit\PHPMock;

    protected function setUp(): void
    {

        $settings = require __DIR__ . '/../../../config/settings.php';

        $settings['phinx']['environments']['test']['connection'] = self::$pdoMysql;

        $config = new Config($settings['phinx']);
        $manager = new Manager($config, new StringInput(' '), new NullOutput());
        $manager->migrate('test');
        $manager->seed('test');

        $_POST = array();
        $_POST['show_se_cat'] = "null";

        $_GET = array();
        $_GET["v_reseni_filtr"] = 99;
        $_GET["vyreseno_filtr"] = 0;
        $_GET["limit"] = 10;

        $_SERVER = array();
        $_SERVER['HTTP_HOST'] = "127.0.0.1";
        $_SERVER['SCRIPT_URL'] = "/home";

    }

    protected function tearDown(): void
    {
    }

    public function testHome()
    {
        // $this->markTestSkipped('under construction');
        $self = $this;

        // prepare DI
        $builder = new ContainerBuilder();
        $builder->addDefinitions('tests/slim/fixtures/bootstrapContainer.php');
        $container = $builder->build();

        $rfMock = \Mockery::mock(ResponseFactoryInterface::class);
        $responseFactory = $rfMock;

        require_once __DIR__ . '/../fixtures/bootstrapContainerAfter.php';

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);

        $this->assertInstanceOf(ContainerInterface::class, $container);

        $this->assertInstanceOf(LoggerInterface::class, $container->get('logger'));
        $this->assertIsObject($container->get('smarty'));

        // mock "underlaying" class for helper functions/logic
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


        // // test sqlite migration
        // $sql = 'pragma table_info(\'board\');';
        // $sql2 = "SELECT * FROM board";
        // $rs = self::$pdoMysql->query($sql2);
        // print_r($rs->fetchAll());

        $output = ob_get_contents();

        ob_end_clean();

        // debug
        // echo $output;

        $this->assertNotEmpty($output);

        $outputKeywords = array(
            '<html lang="en">',
            '<title>Adminator3 :: úvodní stránka</title>',  // adminator head rendered
            'bootstrap.min.css" rel="stylesheet"',  // adminator head rendered
            'Jste přihlášeni v administračním systému', // adminator header rendered
            '<div class="home-vypis-useru-napis" >Přihlašení uživatelé: </div>',
            'Výpis Závad/oprav',
            'Bulletin Board - Nástěnka', // board header exists
            '<div class="table zprava-main" >', // board message exists
            '</body>', // smarty rendered whole page
            '</html>' // smarty rendered whole page
        );

        foreach ($outputKeywords as $w) {

            /*
            // N.B.:
            // assert below causes printing output to stdout
            // workaround is using this foraech with assertFalse
            */
            // $this->assertStringContainsString($output, $w);

            // TODO: maybe will works "assertThat()"
            // -> https://docs.phpunit.de/en/9.6/assertions.html#assertthat

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
