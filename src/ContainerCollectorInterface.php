<?php

namespace Bermuda\App;

use Psr\Container\ContainerInterface;

interface ContainerCollectorInterface
{
    public function add(ContainerInterface $container): void ;
}
