<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\App\Boot\Bootable;
use Bermuda\ClassFinder\ClassFinder;
use Bermuda\ClassFinder\ClassFinderInterface;
use Bermuda\ClassFinder\ClassFoundListenerProviderInterface;
use Bermuda\ClassFinder\FinalizedListenerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Finder implements Bootable
{
    public const string CONFIG_KEY_DIRS = 'Bermuda\App\Boot\Finder:dirs';
    public const string CONFIG_KEY_EXCLUDE = 'Bermuda\App\Boot\Finder:exclude';

    /**
     * @throws \ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot(AppInterface $app): void
    {
        if (!$app->config::$devMode && file_exists(getcwd().'/config/cache/finder.php')) {
            $classes = include getcwd().'/config/cache/finder.php';
        } else {
            $finder = new ClassFinder(ClassFinderInterface::MODE_FIND_ALL);

            $classes = $finder->find(
                $app->config->get(self::CONFIG_KEY_DIRS, [getcwd().'/src']),
                $app->config->get(self::CONFIG_KEY_EXCLUDE, [])
            );
        }


        $provider = $app->get(ClassFoundListenerProviderInterface::class);

        foreach ($classes as $class) {
            foreach ($provider->getClassFoundListeners() as $listener) {
                $listener->handle($class);
            }
        }

        foreach ($provider->getClassFoundListeners() as $listener) {
            if ($listener instanceof FinalizedListenerInterface) $listener->finalize();
        }
    }
}
