<?php

namespace Bermuda\App;

use Bermuda\App\Boot\Bootable;
use Bermuda\App\Boot\BootableCollector;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\{ArgvInput, InputInterface};
use Symfony\Component\Console\Output\{ConsoleOutput, OutputInterface};
use Bermuda\App\Console\CommandResolver;
use Bermuda\App\Console\CommandResolverInterface;
use Bermuda\App\Console\CommandRunnerInterface;
use Bermuda\App\Console\SymfonyConsole;

final class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    public const CONFIG_KEY_CONTAINERS = 'app.containers';

    /**
     * @inheritDoc
     */
    protected function getFactories(): array
    {
        return [
            Bootable::class => [BootableCollector::class, 'createFromContainer'],
            InputInterface::class => [ConfigProvider::class, 'createArgvInput'],
            OutputInterface::class => [ConfigProvider::class, 'createConsoleOutput'],
            CommandRunnerInterface::class => [SymfonyConsole::class, 'createFromContainer'],
            CommandResolverInterface::class => [CommandResolver::class, 'createFromContainer'],
        ];
    }

    public static function createArgvInput(): ArgvInput
    {
        return new ArgvInput;
    }

    public static function createConsoleOutput(): ConsoleOutput
    {
        return new ConsoleOutput;
    }
}
