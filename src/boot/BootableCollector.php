<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Psr\Container\ContainerInterface;

use function Bermuda\Config\conf;

final class BootableCollector implements Bootable
{
    public const string CONFIG_KEY_BOOTABLES = 'Bermuda\App\Boot\Bootable:bootables';
    public const string CONFIG_KEY_EXCLUDE = 'Bermuda\App\Boot\Bootable:exclude';
    
    public const array defaults = [
        PathHelper::class,
        Routing::class,
        Finder::class,
        Pipeline::class,
        ErrorHandling::class,
        Http::class,
        Configurator::class,
        DateTime::class,
        Renderer::class
    ];

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
    
    public static function createFromContainer(ContainerInterface $container): self
    {
        $bootable = array_merge(self::defaults,
            ($config = conf($container))->get(self::CONFIG_KEY_BOOTABLES, [])
        );
        
        $self = new self();
        $exclude = $config->get($self::CONFIG_KEY_EXCLUDE, []);
        
        if ($exclude !== []) {
            foreach ($bootable as $bootable) {
                if (!in_array($bootable, $exclude)) $self->add(new $bootable);
            }
            
            return $self;
        }
        
        foreach ($bootable as $bootable) {
            $self->add(new $bootable);
        }
        
        return $self;
    }

    /**
     * @inerhitDoc
     */
    public static function withDefaults(ContainerInterface $container): self
    {
        return new self([
            new PathHelper,
            new Routing,
            new Finder,
            new Pipeline,
            new ErrorHandling,
            new Http,
            new Configurator,
            new DateTime,
            new Renderer
        ]);
    }
}
