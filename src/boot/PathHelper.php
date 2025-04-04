<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;

class PathHelper implements Bootable
{
    public function boot(AppInterface $app): void
    {

        $app->registerCallback('path', static function (null|array|string $paths = null) use ($app): string {
            if (null === $paths) return getcwd();
            is_array($paths) ?: $paths = [$paths];
            $path = getcwd();
            foreach ($paths as $segment) $path .= DIRECTORY_SEPARATOR . trim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $segment), '\/');
            return $path;
        });
    }
}
