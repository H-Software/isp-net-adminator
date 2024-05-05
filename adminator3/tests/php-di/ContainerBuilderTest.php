<?php

declare(strict_types=1);

namespace DI\Test\UnitTest;

use DI\CompiledContainer;
use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionArray;
use DI\Definition\Source\SourceCache;
use DI\Definition\ValueDefinition;
use DI\Test\IntegrationTest\BaseContainerTest;
use DI\Test\UnitTest\Fixtures\FakeContainer;
use EasyMock\EasyMock;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \DI\ContainerBuilder
 */
class ContainerBuilderTest extends TestCase
{
    use EasyMock;

    private static function getProperty(object $object, string $propertyName)
    {
        return (function (string $propertyName) {
            return $this->$propertyName;
        })->bindTo($object, $object)($propertyName);
    }

    /**
     * @test
     */
    public function should_configure_for_development_by_default()
    {
        // Make the ContainerBuilder use our fake class to catch constructor parameters
        $builder = new ContainerBuilder(FakeContainer::class);
        /** @var FakeContainer $container */
        $container = $builder->build();

        // Not compiled
        $this->assertNotInstanceOf(CompiledContainer::class, $container);
        // Proxies evaluated in memory
        $this->assertNull(self::getProperty($container->proxyFactory, 'proxyDirectory'));
    }

    /**
     * @test
     */
    // public function should_allow_to_configure_a_cache()
    // {
    //     if (! SourceCache::isSupported()) {
    //         $this->markTestSkipped('APCu extension is required');
    //         return;
    //     }

    //     $builder = new ContainerBuilder(FakeContainer::class);
    //     $builder->enableDefinitionCache();

    //     /** @var FakeContainer $container */
    //     $container = $builder->build();

    //     $this->assertInstanceOf(SourceCache::class, $container->definitionSource);
    // }

}
