<?php

declare(strict_types=1);

namespace App\View;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class CsrfExtension.
 */
class CsrfExtension extends AbstractExtension
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param ContainerInterface $container The container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        $c = new CsrfRuntime($this->container->get('csrf'));

        return [
            new TwigFunction('csrf', [$c, 'csrf'], ['is_safe' => ['html']]),
        ];
    }
}
