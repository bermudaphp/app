<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Psr\Container\ContainerInterface;

final class BootableCollector implements Bootable
{
    /**
     * @var Bootable[]
     */
    private array $bootables = [];

    public function __construct(iterable $bootables = [])
    {
        foreach ($bootables as $bootable) $this->add($bootable);
    }

    /**
     * @inerhitDoc
     */
    public function boot(AppInterface $app): void
    {
        foreach ($this->bootables as $bootable) $bootable->boot($app);
    }

    public function add(Bootable $bootable): self
    {
        $this->bootables[] = $bootable;
        return $this;
    }

    /**
     * @inerhitDoc
     */
    public static function withDefaults(ContainerInterface $container): self
    {
        return new self([
            new Routing,
            new Pipeline,
            new ErrorHandling,
            new Http,
            new Configurator,
            new DateTime,
            new Renderer
        ]);
    }
}
