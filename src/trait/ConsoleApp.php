<?php

namespace Bermuda\App\Trait;

use Bermuda\App\AppInterface;
use DI\Definition\Source\MutableDefinitionSource;
use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ConsoleApp
{
    use App { bindEntries as bindEntriesSuper; }

    protected Console\CommandRunnerInterface $runner;
    protected Console\CommandResolverInterface $resolver;

    protected function bindEntries(): void
    {
        $this->bindEntriesSuper();
        $this->runner = $this->get(Console\CommandRunnerInterface::class);
        $this->resolver = $this->get(Console\CommandResolverInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function pipe(mixed $any): AppInterface
    {
        try {
            $this->runner->add($this->resolver->resolve($any));
        } catch (Console\UnresolvableCommandException $e) {
            Console\UnresolvableCommandException::reThrow($e, debug_backtrace()[0]);
        }

        return $this;
    }

    protected function doRun(): void
    {
        $this->runner->run(
            $this->get(InputInterface::class),
            $this->get(OutputInterface::class)
        );
    }
}