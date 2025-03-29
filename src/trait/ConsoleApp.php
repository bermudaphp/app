<?php

namespace Bermuda\App\Trait;

use Bermuda\App\AppInterface;
use Bermuda\App\Console\UnresolvableCommandException;
use DI\Definition\Source\MutableDefinitionSource;
use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Bermuda\App\Console\CommandRunnerInterface;
use Bermuda\App\Console\CommandResolverInterface;

trait ConsoleApp
{
    use App { bindEntries as bindEntriesSuper; }

    protected CommandRunnerInterface $runner;
    protected CommandResolverInterface $resolver;

    protected function bindEntries(): void
    {
        $this->bindEntriesSuper();
        $this->runner = $this->get(CommandRunnerInterface::class);
        $this->resolver = $this->get(CommandResolverInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function pipe(mixed $any): AppInterface
    {
        try {
            $this->runner->add($this->resolver->resolve($any));
        } catch (UnresolvableCommandException $e) {
            UnresolvableCommandException::reThrow($e, debug_backtrace()[0]);
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
