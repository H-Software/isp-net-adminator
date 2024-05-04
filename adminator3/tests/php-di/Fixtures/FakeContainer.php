<?php

declare(strict_types=1);

namespace DI\Test\UnitTest\Fixtures;

use DI\Definition\Source\DefinitionSource;
use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;

/*
*   source
*   https://github.com/PHP-DI/PHP-DI/blob/master/tests/UnitTest/Fixtures/FakeContainer.php
*/

/**
 * Fake container class that exposes all constructor parameters.
 *
 * Used to test the ContainerBuilder.
 */
class FakeContainer
{
    /**
     * @var DefinitionSource
     */
    public $definitionSource;

    /**
     * @var ProxyFactory
     */
    public $proxyFactory;

    /**
     * @var ContainerInterface
     */
    public $wrapperContainer;

    public function __construct(
        DefinitionSource $definitionSource,
        ProxyFactory $proxyFactory,
        ContainerInterface $wrapperContainer = null
    ) {
        $this->definitionSource = $definitionSource;
        $this->proxyFactory = $proxyFactory;
        $this->wrapperContainer = $wrapperContainer;
    }
}
