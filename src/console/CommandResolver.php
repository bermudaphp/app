<?php

namespace Bermuda\App\Console;

use Psr\Container\ContainerInterface;

/**
 * Class CommandResolver
 * @package Bermuda\App\Console
 */
final class CommandResolver implements CommandResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function resolve($any): CommandInterface
    {
        if (is_string($any) && $this->container->has($any))
        {
            $any = $this->container->get($any);
        }

        if ($any instanceof CommandInterface)
        {
            return $any;
        }

        throw new UnresolvableCommandException(null, $any);
    }
}
