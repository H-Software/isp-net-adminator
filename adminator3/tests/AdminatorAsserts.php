<?php

// https://github.com/laminas/laminas-form/blob/8e3cc77c54774b9b542f52b4df5953411e0a7ab7/test/Integration/FormCreatesCollectionInputFilterTest.php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

final class AdminatorAssert extends TestCase
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

    public static function assertBase()
    {

    }

    public static function assertTopologySubCat($content)
    {
        self::assertMatchesRegularExpression('/<a class="cat2" href="\/topology\/router-list">Routery<\/a>/i', $content);
        self::assertMatchesRegularExpression('/<a class="cat2" href="\/topology\/node-list">Výpis lokalit\/nodů<\/a>/i', $content);
        self::assertMatchesRegularExpression('/<a class="cat2" href="topology-user-list.php">Výpis objektů dle přiřazení \/ dle nodů<\/a>/i', $content);
    }

    public static function assertTopologyNodeListNoDataFound($content)
    {
        self::assertMatchesRegularExpression('/class="alert\s*alert-warning"\s*role="alert"/i', $content, "missing no-data message container");
        self::assertMatchesRegularExpression('/Žadné lokality\/nody dle hladeného výrazu \( %.*% \) v databázi neuloženy/i', $content, "missing no-data message");
    }
}
