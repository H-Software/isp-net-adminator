<?php

// https://github.com/laminas/laminas-form/blob/8e3cc77c54774b9b542f52b4df5953411e0a7ab7/test/Integration/FormCreatesCollectionInputFilterTest.php

declare(strict_types=1);

namespace App\Tests;

// use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class AdminatorAssert extends AdminatorTestCase
{
    // public static function assertValidatorFound(string $class, array $validators, ?string $message = null): void
    // {
    //     $message = $message ?: sprintf('Failed to find validator of type %s in validator list', $class);
    //     foreach ($validators as $instance) {
    //         $validator = $instance['instance'];
    //         if ($validator instanceof $class) {
    //             return;
    //         }
    //     }
    //     self::fail($message);
    // }

    public static function assertBaseCommon($responseContent)
    {
        // common stuff for smarty and twig templates

        self::assertNotEmpty($responseContent);

        $assertKeywordsCommon = array(
            '<html lang="en">',
            '<title>Adminator3',  // adminator head rendered
            'bootstrap.min.css" rel="stylesheet"',  // bootstrap
            'bootstrap.bundle.min.js" integrity="', // bootstrap JS
            'jquery.min.js">', // bootstrap JS requirements
            '</body>', // rendered whole page
            '</html>' // rendered whole page
        );

        foreach ($assertKeywordsCommon as $w) {

            self::assertStringContainsString($w, $responseContent, "missing string \"" . $w . "\" in response body");

            // if (!str_contains($responseContent, $w)) {
            //     $this->assertFalse(true, "missing string \"" . $w . "\" in controller output");
            // }
        }
    }

    public static function assertBase($responseContent)
    // protected function runBasicAsserts($responseContent)
    {
        self::assertBaseCommon($responseContent);

        $assertKeywordsCommon = array(
            'link href="/public/css/style.css" rel="stylesheet" type="text/css" ',
            'Jste přihlášeni v administračním systému', // adminator header rendered
            '<span class="intro-banner-logged"', // logged details container
            '<div id="obsah" >', // main container
            '<a class="cat" href="/vlastnici/cat" target="_top" >Zákazníci</a>', // categories - 1.line
            '<a class="cat" href="/partner/cat" target="_top" >Partner program</a>', // categories - 2.line
            '<div class="obsah-main" >', // inner container
        );

        foreach ($assertKeywordsCommon as $w) {

            self::assertStringContainsString($w, $responseContent, "missing string \"" . $w . "\" in response body");

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
            self::assertStringNotContainsStringIgnoringCase($w, $responseContent, "found word (" . $w. "), which indicates error(s) or failure(s)");
        }

        // test sqlite migration
        // $sql = 'pragma table_info(\'board\');';
        // $sql2 = "SELECT * FROM board";
        // $rs = self::$pdoMysql->query($sql2);
        // print_r($rs->fetchAll());

        // debug
        // echo $responseContent;
    }

    public static function assertNoLevelPage($response)
    {
        $responseContent = $response->getBody()->__toString();

        self::assertEquals($response->getStatusCode(), 403);

        self::assertStringContainsString("Nelze zobrazit požadovanou stránku", $responseContent, "missing string 1 in response body");
        self::assertStringContainsString("Pro otevřetí této stránky nemáte dostatečné oprávnění (level).", $responseContent, "missing string 2 in response body");
    }

    public static function assertHomePagePanels($response, $responseContent)
    {
        $assertKeywordsHome = array(
            '<div class="home-vypis-useru-napis" >Přihlašení uživatelé: </div>', // loggeduser banner
            'uživatel: <span class="home-vypis-useru-font1" >', // logger user row
            'Výpis Závad/oprav',
        );

        foreach ($assertKeywordsHome as $w) {
            self::assertStringContainsString($w, $responseContent, "missing string \"" . $w . "\" in response body");
        }

        adminatorAssert::assertBoardCommon($response, $responseContent);
        adminatorAssert::assertBoardMessages($response, $responseContent);
    }

    public static function assertBoardCommon($response, $responseContent)
    {
        // board header
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[2]/div[2]/div', '/^Bulletin.*Board.*/');
        // link RSS with token
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[3]/div[2]/div/div[6]/span/a', '/^\/board\/rss\?token=[[:alnum:]]{10,}$/');
        // self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[3]/div[2]/div/div[6]/span/a', '/^\/board\/rss\?token=$/');
    }

    public static function assertBoardMessages($response, $responseContent)
    {
        // header
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[2]', '/^zpráva č. [[:digit:]]{1,}/');
        // subject
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[3]/b', '/^([[:word:]]|[[:space:]]){5,}/');
        // body
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[4]/div[2]/div[3]/div', '/^([[:word:]]|[[:space:]]){15,}/');

        // page number/listing
        self::assertXpathQueryContentRegex($response, '//*[@id="board-list-pagging"]/b', '/strana\s*\|/');
    }

    public static function assertPartnerSubCat(ResponseInterface $response)
    {
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[1]/a', '/^Připojování nových klientů$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[1]/a', '/^\/partner\/order$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[2]/a', '/^Servisní zásahy$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[2]/a', '/^\/partner\/servis\/list$/');
    }

    public static function assertPartnerOrderSubCat($response)
    {
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[1]/a', '/^| O úrověn výš \|$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[1]/a', '/^\/partner\/cat$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[2]/a', '/^Vložení žádosti$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[2]/a', '/^\/partner\/order\/add$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[3]/a', '/^Výpis žádostí$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[3]/a', '/^\/partner\/order\/list$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[4]/a', '/^Akceptování žádosti$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[4]/a', '/^\/partner\/order\/accept$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[5]/a', '/^Změna stavu připojení$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[5]/a', '/^\/partner\/order\/change-status$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[6]/a', '/^Změna poznámky$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[6]/a', '/^\/partner\/order\/change-desc$/');
    }

    public static function assertOtherCat(ResponseInterface $response)
    {
        // level up
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[1]/a', '/^| O úrověn výš | $/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[1]/a', '/^\/home$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[2]/a', '/^Nástěnka$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[2]/a', '/^\/others\/board$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[3]/a', '/^Tisk$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[3]/a', '/^\/print$/');

        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[4]/a', '/^Company Web$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[4]/a', '/^\/others\/company-web$/');
    }

    public static function assertTopologySubCat($response, $content)
    {
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[1]/a[2]', '/^přidání$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[1]/span[1]/a[2]', '/^\/topology\/router\/action$/');

        self::assertMatchesRegularExpression('/<a class="cat2" href="\/topology\/router-list">Routery<\/a>/i', $content);
        self::assertMatchesRegularExpression('/<a class="cat2" href="\/topology\/node-list">Výpis lokalit\/nodů<\/a>/i', $content);
        self::assertMatchesRegularExpression('/<a class="cat2" href="topology-user-list.php">Výpis objektů dle přiřazení \/ dle nodů<\/a>/i', $content);
    }

    public static function assertTopologyNodeListHeaderAndSelectors($responseContent)
    {
        self::assertMatchesRegularExpression('/Výpis lokalit\s*\/\s*přípojných bodů/i', $responseContent);
        self::assertMatchesRegularExpression('/Hledání\:/i', $responseContent);
        self::assertStringContainsString('<select name="typ_vysilace"', $responseContent);
        self::assertStringContainsString('<select name="typ_nodu" size="1"', $responseContent);
    }

    public static function assertTopologyNodeListTableHeader($responseContent)
    {
        self::assertStringContainsString('Umístění aliasu (název routeru):', $responseContent);
        self::assertStringContainsString('Stav: </span>', $responseContent);
        self::assertStringContainsString('Úprava / Smazání:', $responseContent);
        self::assertStringContainsString('<select name="typ_nodu" size="1"', $responseContent);
    }

    public static function assertTopologyNodeListNoDataFound($content)
    {
        self::assertMatchesRegularExpression('/class="alert\s*alert-warning"\s*role="alert"/i', $content, "missing no-data message container");
        self::assertMatchesRegularExpression('/Žadné lokality\/nody dle hladeného výrazu \( %.*% \) v databázi neuloženy/i', $content, "missing no-data message");
    }

    public static function assertTopologyRouterListHeaderAndSelectors($response)
    {
        self::assertXpathQueryContentRegex($response, '//*[@id="obsah"]/div[5]/div[2]/div[1]/span[1]', '/^.:: Výpis routerů ::.$/');
        // self::assertXpathQueryContentRegex($response, '//*[@id="routers_filter"]/div[9]/input', '/^OK$/');
        self::assertXpathQueryContentRegex($response, '//*[@id="routers_filter"]/div[11]', '/^Hledání: $/');
    }
}
