<?php

namespace Bermuda\App;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\{ArgvInput, InputInterface};
use Symfony\Component\Console\Output\{ConsoleOutput, OutputInterface};
use Bermuda\App\Console\CommandResolver;
use Bermuda\App\Console\CommandResolverInterface;
use Bermuda\App\Console\CommandRunnerInterface;
use Bermuda\App\Console\SymfonyConsole;
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
            CommandRunnerInterface::class => static fn() => new SymfonyConsole,
            CommandResolverInterface::class => static fn(ContainerInterface $container) => new CommandResolver($container)
        ];
    }
}
