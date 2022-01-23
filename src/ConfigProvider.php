<?php

namespace Bermuda\App;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\{ArgvInput, InputInterface};
use Symfony\Component\Console\Output\{ConsoleOutput, OutputInterface};
use Bermuda\Config\ConfigProvider as AbstractProvider;

final class ConfigProvider extends AbstractProvider
{
    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
          InputInterface::class => static fn() => new ArgvInput,
          OutputInterface::class => static fn() => new ConsoleOutput,
          Console\CommandRunnerInterface::class => static fn() => new Console\SymfonyConsole,
          Console\CommandResolverInterface::class => static fn(ContainerInterface $container) => new Console\CommandResolver($container)
        ];
    }
}
