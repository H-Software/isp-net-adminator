<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

abstract class AdminatorTestCase extends TestCase
{
    public static $pdoMysql;

    public static function setUpBeforeClass(): void
    {
        require __DIR__ . "/fixtures/bootstrapDatabase.php";

        self::$pdoMysql = $capsule->connection("default")->getPdo();
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdoMysql = null;
    }
}
